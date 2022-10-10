<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index() {

        $user = User::find(Auth::user()->id);
        $productIds = $user->store->products()->pluck('id')->toArray();
        $orders = Order::whereHas('orderDetails', function ($query) use ($productIds) {
            $query->whereIn('product_id', $productIds);
        })->orderBy('created_at', 'desc')->get();

        return view('orders.index', compact('orders'));
    }

    public function details(Request $request){
        $user = User::find(Auth::user()->id);
        $productIds = $user->store->products()->pluck('id')->toArray();
        $order = Order::find($request->order_id);
        $details = OrderDetail::where('order_id', $order->id)
                        ->whereIn('product_id', $productIds)
                        ->get();

        $detailsOtherStore = OrderDetail::where('order_id', $order->id)
                        ->whereNotIn('product_id', $productIds)
                        ->get();

        return view('orders.details', compact('order', 'details', 'detailsOtherStore'));
    }

}
