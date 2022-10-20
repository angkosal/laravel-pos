<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
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
    // dashboard route
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');

    // products routes
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{product_id}/details', [ProductController::class, 'details'])->name('products.details');

    // orders routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order_id}/details', [OrderController::class, 'details'])->name('orders.details');

    // pos page route
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/products', [CartController::class, 'getProducts']);
    Route::get('/cart/get_student_orders', [CartController::class, 'getStudentOrders']);
    Route::get('/cart/get_products_barcode', [CartController::class, 'getProductsByBarcode']);
    Route::post('/cart/store', [CartController::class, 'store']);
});
