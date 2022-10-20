<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $productIds = $user->store->products()->pluck('id')->toArray();
        $ordersCount = Order::whereHas('orderDetails', function ($query) use ($productIds) {
            $query->whereIn('product_id', $productIds);
        })->whereDate('pick_up_start', Carbon::today())->count();

        $productsPickUpCount = OrderDetail::whereHas('order', function ($query) {
            $query->whereDate('pick_up_start', Carbon::today());
        })->count();
        $productsPickedUpCount = OrderDetail::whereHas('order', function ($query) {
            $query->whereDate('pick_up_start', Carbon::today());
        })->where('is_pickup', true)->count();

        return view('home', compact('ordersCount', 'productsPickUpCount', 'productsPickedUpCount'));
    }

}
