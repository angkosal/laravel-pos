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
    public function index(Request $request) {
        // $orders = new Order();
        // if($request->start_date) {
        //     $orders = $orders->where('created_at', '>=', $request->start_date);
        // }
        // if($request->end_date) {
        //     $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        // }
        // $orders = $orders->with(['items', 'payments', 'customer'])->latest()->paginate(10);

        // $total = $orders->map(function($i) {
        //     return $i->total();
        // })->sum();
        // $receivedAmount = $orders->map(function($i) {
        //     return $i->receivedAmount();
        // })->sum();

        $user = User::find(Auth::user()->id);
        $productIds = $user->store->products()->pluck('id')->toArray();
        $orders = Order::whereHas('orderDetails', function ($query) use ($productIds) {
            $query->whereIn('product_id', $productIds);
        })->get();

        //dd($orders);

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

    public function store(OrderStoreRequest $request)
    {
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'user_id' => $request->user()->id,
        ]);

        $cart = $request->user()->cart()->get();
        foreach ($cart as $item) {
            $order->items()->create([
                'price' => $item->price * $item->pivot->quantity,
                'quantity' => $item->pivot->quantity,
                'product_id' => $item->id,
            ]);
            $item->quantity = $item->quantity - $item->pivot->quantity;
            $item->save();
        }
        $request->user()->cart()->detach();
        $order->payments()->create([
            'amount' => $request->amount,
            'user_id' => $request->user()->id,
        ]);
        return 'success';
    }
}
