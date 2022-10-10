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
    Route::get('/cart/products', [CartController::class, 'getProducts']);
    Route::get('/cart/get_student_orders', [CartController::class, 'getStudentOrders']);
    Route::get('/cart/get_products_barcode', [CartController::class, 'getProductsByBarcode']);
    Route::post('/cart/store', [CartController::class, 'store']);
});
