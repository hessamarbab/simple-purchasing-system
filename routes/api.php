<?php

use App\Http\Controllers\OrderController;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Payment\PaymentRepositoryContract;
use App\Repositories\Product\ProductRepositoryContract;
use App\Repositories\User\UserRepositoryContract;
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
    $userRepo = app()->make(UserRepositoryContract::class);
    return $userRepo->all();
});

Route::get('products', function () {
    $productRepo = app()->make(ProductRepositoryContract::class);
    return $productRepo->all();
});

Route::get('payments', function () {
    $payRepo = app()->make(PaymentRepositoryContract::class);
    return $payRepo->all();
});

Route::prefix('orders/')->controller(OrderController::class)->group(function () {
    Route::get('', 'all');
});
