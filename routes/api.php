<?php

use App\Http\Controllers\OrderController;
use App\Repositories\Payment\PaymentRepositoryContract;
use App\Repositories\Product\ProductRepositoryContract;
use App\Repositories\User\UserRepositoryContract;
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
    /** @var App\Repositories\User\UserRepositoryCachingDecorator $userRepo */
    $userRepo = app()->make(UserRepositoryContract::class);
    return $userRepo->all();
});

Route::get('products', function () {
    /** @var App\Repositories\Product\ProductRepositoryCachingDecorator $productRepo */
    $productRepo = app()->make(ProductRepositoryContract::class);
    return $productRepo->all();
});

Route::get('payments', function () {
    /** @var App\Repositories\Payment\PaymentRepositoryCachingDecorator $payRepo */
    $payRepo = app()->make(PaymentRepositoryContract::class);
    return $payRepo->all();
});

Route::prefix('orders/')->controller(OrderController::class)->group(function () {
    Route::get('', 'all');
    Route::post('reserve', 'reserve');
    Route::get('return_bank/{bank_kind}/{payment_code}', 'confirm')->name('return-bank');
});
