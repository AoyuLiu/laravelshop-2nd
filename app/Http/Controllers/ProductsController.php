<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Category;
use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItem;
use App\Services\CategoryService;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page',1);

        $perPage = 16;

        $params = [
            'index' => 'products',
            'type' => '_doc',
            'body' => [
                'from' => ($page -1)*$perPage,
                'size' => $perPage,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['on_sale' => true]],
                        ],
                    ],
                ],
            ],
        ];

         if ($search = $request->input('search', '')) {
            // 将搜索词根据空格拆分成数组，并过滤掉空项
            $keywords = array_filter(explode(' ', $search));

            $params['body']['query']['bool']['must'] = [];
            // 遍历搜索词数组，分别添加到 must 查询中
            foreach ($keywords as $keyword) {
                $params['body']['query']['bool']['must'][] = [
                    'multi_match' => [
                        'query'  => $keyword,
                        'fields' => [
                            'title^2',
                            'long_title^2',
                            'category^2',
                            'description',
                            'skus.title^2',
                            'skus.description',
                            'properties.value',
                        ],
                    ],
                ];
            }
        }

        if ($search || isset($category)) {
            $params['body']['aggs'] = [
                'properties' => [
                    'nested' => [
                        'path' => 'properties',
                    ],
                    'aggs'  => [
                        'properties' => [
                            'terms' => [
                                'field' => 'properties.name',
                            ],
                            'aggs' => [
                                'value' => [
                                    'terms' => [
                                        'field' => 'properties.value',
                                    ],
                                ],
                            ],
                        ],

                    ],
                ],
            ];
        }

        if ($order = $request->input('order','')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price','sold_count','rating'])) {
                    $params['body']['sort'] = [[$m[1]=> $m[2]]];
                }
            }
        }

        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            if ($category->is_directory) {
                
                $params['body']['query']['bool']['filter'][] = [
                    'prefix' => ['category_path' => $category->path.$category->id.'-'],
                ];
            } else {
                $params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
            }
        }

        $propertyFilters = [];
        if ($filterString = $request->input('filters')) {
            $filterArray = explode('|', $filterString);
            foreach ($filterArray as $filter) {
                list($name,$value) = explode(':', $filter);
                $propertyFilters[$name] = $value;
                $params['body']['query']['bool']['filter'][] = [
                    'nested' => [
                        'path' => 'properties',
                        'query' => [
                            ['term' => ['properties.search_value' => $filter]],
                        ],
                    ],
                ];
            }
        }
        $result = app('es')->search($params);

        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
        $products = Product::query()
                ->whereIn('id', $productIds)
                ->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $productIds)))
                ->get();

        $pager = new LengthAwarePaginator($products, $result['hits']['total'],$perPage, $page,[
            'path' => route('products.index',false),
        ]);

        $properties = [];

        if (isset($result['aggregations'])) {
            
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
                    ->map(function($bucket) {
                        return [
                            'key' => $bucket['key'],
                            'values' => collect($bucket['value']['buckets'])->pluck('key')->all(),
                        ];
                    })
                    ->filter(function($property) use ($propertyFilters){
                         return count($property['values']) > 1 && !isset($propertyFilters[$property['key']]);
                    });
        }

        return view('products.index',[
            'products' => $pager,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
            'category' => $category ?? null,
            'properties' => $properties,
            'propertyFilters' => $propertyFilters,
        ]);
    }

    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        
        if($user = $request->user()) {
           
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }
        
        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku']) 
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at') 
            ->orderBy('reviewed_at', 'desc') 
            ->limit(10) 
            ->get();
        
        
        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews
        ]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }

    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }
}
