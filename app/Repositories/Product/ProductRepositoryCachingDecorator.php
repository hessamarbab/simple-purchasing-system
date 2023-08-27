<?php

namespace App\Repositories\Product;

use App\Exceptions\CustomizedException;
use Illuminate\Support\Facades\Cache;

class ProductRepositoryCachingDecorator implements ProductRepositoryContract
{
    const PRODUCT_CACHE_PREFIX = 'PRODUCT_';

    /**
     * @param ProductRepositoryContract $productRepository
     */
    public function __construct(
        protected int $ttl = 60,
        protected ProductRepositoryContract $productRepository  = new ProductEloquentRepository()
    ){}

    public function all()
    {
        // it's not ok to cache all things
        return $this->productRepository->all();
    }

    public function reduce(int $product_id, int $quantity)
    {
        $cacheKey = self::PRODUCT_CACHE_PREFIX . $product_id;
        $product = Cache::get($cacheKey);
        if ($product != null) {
            $product->inventory -= $quantity;
            if($product->inventory < 0) {
                throw new CustomizedException("there isn't enough inventory");
            }
            Cache::put($cacheKey, $product, $this->ttl);
        }
        $this->productRepository->reduce($product_id, $quantity);

    }

    public function getById(int $id): array
    {
        $cacheKey = self::PRODUCT_CACHE_PREFIX . $id;
        return Cache::remember(
            $cacheKey,
            $this->ttl,
            function () use ($id) {
                return $this->productRepository->getById($id);
            }
        );
    }

    public function enhance(int $product_id, int $quantity)
    {
        $cacheKey = self::PRODUCT_CACHE_PREFIX . $product_id;
        $product = Cache::get($cacheKey);
        if ($product != null) {
            $product->inventory += $quantity;
            Cache::put($cacheKey, $product, $this->ttl);
        }
        $this->productRepository->enhance($product_id, $quantity);
    }
}
