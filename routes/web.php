<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('admin')->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::resource('products', 'ProductController');
    Route::resource('customers', 'CustomerController');
});
