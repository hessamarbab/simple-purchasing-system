<?php

namespace App\Providers;

use App\Driver\Ipg\IpgDriver;
use App\Driver\Ipg\IpgDriverContract;
use App\Repositories\Atomic\DbTransactionRepositoryCacheDecorator;
use App\Repositories\Atomic\DbTransactionRepositoryContract;
use App\Repositories\Order\OrderRepositoryCachingDecorator;
use App\Repositories\Order\OrderRepositoryContract;
use App\Repositories\Payment\PaymentRepositoryCachingDecorator;
use App\Repositories\Payment\PaymentRepositoryContract;
use App\Repositories\Product\ProductRepositoryCachingDecorator;
use App\Repositories\Product\ProductRepositoryContract;
use App\Repositories\User\UserRepositoryCachingDecorator;
use App\Repositories\User\UserRepositoryContract;
use App\Services\Purchase\PurchaseService;
use App\Services\Purchase\PurchaseServiceContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DbTransactionRepositoryContract::class, function () {
           return new DbTransactionRepositoryCacheDecorator();
        });
        $this->app->bind(UserRepositoryContract::class, function () {
            return new UserRepositoryCachingDecorator(config("cache.default_ttl"));
        });
        $this->app->bind(ProductRepositoryContract::class, function () {
            return new ProductRepositoryCachingDecorator( config("cache.default_ttl"));
        });
        $this->app->bind(PaymentRepositoryContract::class, function () {
            return new PaymentRepositoryCachingDecorator(config("cache.default_ttl"));
        });
        $this->app->bind(OrderRepositoryContract::class, function () {
            return new OrderRepositoryCachingDecorator(config("cache.default_ttl"));
        });
        $this->app->bind(PurchaseServiceContract::class, function () {
            return $this->app->make(PurchaseService::class);
        });
        $this->app->bind(IpgDriverContract::class , function ($app, $params) {
            return new IpgDriver(...$params);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
