<?php

namespace App\Repositories\Product;

use App\Exceptions\CustomizedException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepositoryCachingDecorator implements ProductRepositoryContract
{
    const PRODUCT_CACHE_PREFIX = 'PRODUCT_';

    /**
     * @param ProductRepositoryContract $productRepository
     */
    public function __construct(
        protected int                       $ttl = 60,
        protected ProductRepositoryContract $productRepository = new ProductEloquentRepository()
    )
    {
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        // it's not ok to cache all things
        return $this->productRepository->all();
    }

    public function reduce(int $product_id, int $quantity)
    {
        $cacheKey = self::PRODUCT_CACHE_PREFIX . $product_id;
        $product = Cache::get($cacheKey);
        if ($product != null) {
            $product['inventory'] -= $quantity;
            if ($product['inventory'] < 0) {
                throw new CustomizedException("there isn't enough inventory");
            }
            Cache::put($cacheKey, $product, $this->ttl);
        }
        $this->productRepository->reduce($product_id, $quantity);

    }

    public function getByIds(array $ids): array
    {
        $cachedData = [];
        $notCachedIds = [];
        foreach ($ids as $id) {
            $cacheKey = self::PRODUCT_CACHE_PREFIX . $id;
            $res = Cache::get($cacheKey);
            if ($res == null) {
                array_push($notCachedIds, $id);
            } else {
                $cachedData[$id] = $res;
            }

        }
        $notCachedData = $this->productRepository->getByIds($notCachedIds);
        foreach ($notCachedData as $item) {
            $cacheKey = self::PRODUCT_CACHE_PREFIX . $item["id"];
            Cache::put($cacheKey, $item, $this->ttl);
        }
        return $notCachedData + $cachedData;
    }

    public function enhance(int $product_id, int $quantity)
    {
        $cacheKey = self::PRODUCT_CACHE_PREFIX . $product_id;
        $product = Cache::get($cacheKey);
        if ($product != null) {
            $product['inventory'] += $quantity;
            Cache::put($cacheKey, $product, $this->ttl);
        }
        $this->productRepository->enhance($product_id, $quantity);
    }
}
