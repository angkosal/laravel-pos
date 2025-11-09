<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function __invoke(): Factory|View|\Illuminate\View\View
    {
        $orders = Order::with(['items', 'payments'])->get();

        return view('home', [
            'orders_count' => $orders->count(),
            'income' => $orders->sum(fn($order): float => min($order->receivedAmount(), $order->total())),
            'income_today' => $orders->where('created_at', '>=', today())
                ->sum(fn($order): float => min($order->receivedAmount(), $order->total())),
            'customers_count' => Customer::count(),
            'low_stock_products' => Product::lowStock()->get(),
            'best_selling_products' => Product::bestSelling()->get(),
            'current_month_products' => Product::currentMonthBestSelling()->get(),
            'past_months_products' => Product::pastMonthsHotProducts()->get(),
        ]);
    }
}
