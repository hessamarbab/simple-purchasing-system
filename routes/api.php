<?php

use App\Http\Controllers\OrderController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('users', function () {
    return User::all();
});

Route::get('products', function () {
    return Product::all();
});

Route::prefix('orders/')->controller(OrderController::class)->group(function () {
    Route::get('', 'all');
});
