<?php

namespace App\Http\Controllers;
use App\Services\ProductService;
use App\SearchBuilders\ProductSearchBuilder;
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

        $builder = (new ProductSearchBuilder())->onSale()->paginate($perPage,$page);

        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            $builder->category($category);
        }

        if ($search = $request->input('search', '')) {
            $keywords = array_filter(explode(' ', $search));
            $builder->keywords($keywords);
        }

        if ($search || isset($category)) {
           
           $builder->aggregateProperties();
        }

        $propertyFilters = [];
        if ($filterString = $request->input('filters')) {
            $filterArray = explode('|', $filterString);
            foreach ($filterArray as $filter ) {
                list($name, $value) = explode(':', $filter);
                $propertyFilters[$name] = $value;

                $builder->propertyFilters($name, $value);
            }
        }

        if ($order = $request->input('order','')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price','sold_count','rating'])) {
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        
        $result = app('es')->search($builder->getParams());

        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
        $products = Product::query()->byIds($productIds)->get();

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

    public function show(Product $product, Request $request, ProductService $service)
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
        
         $similarProductIds = $service->getSimilarProductIds($product, 4);

       $similarProductIds = Product::query()->byIds($similarProductIds)->get();

        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews,
            'similar' => $similarProductIds,
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
