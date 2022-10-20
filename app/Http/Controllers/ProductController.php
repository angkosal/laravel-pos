<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Show all the products.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $products = $user->store->products;
        return view('products.index', compact('products'));
    }

    /**
     * Show the given product's detail.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function details(Request $request)
    {
        $product = Product::find($request->product_id);
        return view('products.details', compact('product'));
    }

}
