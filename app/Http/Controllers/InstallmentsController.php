<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Installment;

class InstallmentsController extends Controller
{
    public function index(Request $request)
    {
    	$installments = Installment::query()
    		->where('user_id', $request->user()->id)
    		->paginate(10);

    	return view('installments.index',['installments'=> $installments]);
    }

    public function show(Installment $installment)
    {
    	$items = $installment->items()->OrderBy('sequence')->get();
    	return view('installments.show',[
    		'installment' => $installment,
    		'items'       => $items,
    		'nextItem'    => $items->where('paid_at',null)->first(),

    	]);
    }
}
