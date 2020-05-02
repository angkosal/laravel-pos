<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/settings', 'SettingController@index')->name('settings.index');
    Route::post('/settings', 'SettingController@store')->name('settings.store');
    Route::resource('products', 'ProductController');
    Route::resource('customers', 'CustomerController');

    Route::get('/cart', 'CartController@index')->name('cart.index');
    Route::post('/cart', 'CartController@store')->name('cart.store');
    Route::post('/cart/change-qty', 'CartController@changeQty');
    Route::delete('/cart/delete', 'CartController@delete');
    Route::delete('/cart/empty', 'CartController@empty');
});
