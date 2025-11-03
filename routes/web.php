<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::prefix('admin')->middleware(['auth', 'locale'])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('absensi', AbsensiController::class);

    // --- Laporan ---
    Route::prefix('laporan')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/filter/{periode}', [LaporanController::class, 'filter'])->name('laporan.filter');
    Route::post('/import', [LaporanController::class, 'import'])->name('laporan.import');
    Route::post('/store', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/{id}', [LaporanController::class, 'show'])->name('laporan.show'); // <-- Tambahkan ini
    Route::get('/{id}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/{id}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::delete('/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])->name('laporan.update');
});



    // --- Cart ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);

    // --- Purchase ---
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.cart.index');

    // --- Partial Payment ---
    Route::post('/orders/partial-payment', [OrderController::class, 'partialPayment'])->name('orders.partial-payment');

    // --- Locale & Language Switch ---
    Route::get('/locale/{type}', function ($type) {
        return response()->json(trans($type));
    });

    Route::get('/lang-switch/{lang}', function ($lang) {
        $supportedLocales = ['en', 'es'];
        if (in_array($lang, $supportedLocales)) {
            session(['locale' => $lang]);
            app()->setLocale($lang);
        }
        return redirect()->back();
    })->name('lang.switch');

});