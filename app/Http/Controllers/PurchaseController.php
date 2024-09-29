<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('purchase.index', compact('products', 'suppliers'));
    }
}
