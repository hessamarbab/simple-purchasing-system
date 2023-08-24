<?php

namespace App\Providers;

use App\Repositories\Order\OrderElequentRepository;
use App\Repositories\Order\OrderRepositoryContract;
use App\Repositories\Payment\PaymentEloquentRepository;
use App\Repositories\Payment\PaymentRepositoryCachingDecorator;
use App\Repositories\Payment\PaymentRepositoryContract;
use App\Repositories\Product\ProductEloquentRepository;
use App\Repositories\Product\ProductRepositoryCachingDecorator;
use App\Repositories\Product\ProductRepositoryContract;
use App\Repositories\User\UserEloquentRepository;
use App\Repositories\User\UserRepositoryCachingDecorator;
use App\Repositories\User\UserRepositoryContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryContract::class, function () {
           return new UserRepositoryCachingDecorator(new UserEloquentRepository(), config("cache.default_ttl"));
        });
        $this->app->bind(ProductRepositoryContract::class, function () {
            return new ProductRepositoryCachingDecorator(new ProductEloquentRepository(), config("cache.default_ttl"));
        });
        $this->app->bind(PaymentRepositoryContract::class, function () {
            return new PaymentRepositoryCachingDecorator(new PaymentEloquentRepository(), config("cache.default_ttl"));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
