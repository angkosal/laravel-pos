<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/dashboard');
});

Auth::routes();

Route::get('/logout', function () {
    Auth::logout();
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    //Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    //Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    //Route::resource('products', ProductController::class);
    //Route::resource('customers', CustomerController::class);
    //Route::resource('orders', OrderController::class);

    // products routes
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{product_id}/details', [ProductController::class, 'details'])->name('products.details');

    // orders routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order_id}/details', [OrderController::class, 'details'])->name('orders.details');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);
    Route::get('/cart/products', [CartController::class, 'getProducts']);

    Route::get('/cart/products', [CartController::class, 'getProducts'])->withoutMiddleware(['auth']);
    Route::get('/cart/get_student_orders', [CartController::class, 'getStudentOrders'])->withoutMiddleware(['auth']);
});

Route::get('/test', function () {
    $productIds = \App\Models\Store::find(1)->products()->pluck('id')->toArray();
    $orders = Order::whereHas('student', function ($query) {
        $query->where('student_number', '65300');
    })
        ->whereHas('orderDetails', function ($query) use ($productIds) {
            $query->whereIn('product_id', $productIds);
        })
        ->get();

    foreach ($orders as $order) {
        $details = \App\Models\OrderDetail::where('order_id', $order->id)->whereIn('product_id', $productIds)->get();
        $orders->find($order->id)->order_details = $details;

    }

    dd($orders);
});
