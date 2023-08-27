<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class OrderRepositoryCachingDecorator implements OrderRepositoryContract
{
    const ORDER_CACHE_PREFIX = 'ORDER_';
    const ORDER_ITEMS_CACHE_PREFIX = 'ORDER_ITEMS_';

    /**
     * @param OrderRepositoryContract $orderRepository
     * @param int $ttl
     */
    public function __construct(
        protected int                     $ttl = 60,
        protected OrderRepositoryContract $orderRepository = new OrderEloquentRepository()
    )
    {
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        // it's not ok to cache all things
        return $this->orderRepository->all();
    }

    /**
     * @param int $user_id
     * @return array
     */
    public function create(int $user_id): array
    {
        $order = $this->orderRepository->create($user_id);
        $cacheKey = self::ORDER_CACHE_PREFIX . $order['id'];
        Cache::put($cacheKey, $order, $this->ttl);
        return $order;
    }

    /**
     * @param int $order_id
     * @param int $user_id
     * @param int $product_id
     * @param int $quantity
     * @return void
     */
    public function createItem(int $order_id, int $user_id, int $product_id, int $quantity)
    {
        $orderItem = $this->orderRepository->createItem($order_id, $user_id, $product_id, $quantity);
        try {
            $cacheKey = self::ORDER_ITEMS_CACHE_PREFIX . $order_id;
            $redis = Redis::connection();
            $redis->sadd($cacheKey, serialize([
                'product_id' => $product_id,
                'quantity' => $quantity
            ]));
            $redis->expire($cacheKey, $this->ttl);
        } catch (\Throwable $e) {}
    }

    /**
     * @param int $order_id
     * @return void
     */
    public function apply(int $order_id)
    {
        $cacheKey = self::ORDER_CACHE_PREFIX . $order_id;
        $order = Cache::get($cacheKey);
        if ($order != null) {
            $order->status = OrderStatusEnum::PERFORMED->value;
            Cache::put($cacheKey, $order, $this->ttl);
        }
        $this->orderRepository->apply($order_id);
    }

    /**
     * @param int $order_id
     * @return void
     */
    public function fail(int $order_id)
    {
        $cacheKey = self::ORDER_CACHE_PREFIX . $order_id;
        $order = Cache::get($cacheKey);
        if ($order != null) {
            $order['status'] = OrderStatusEnum::FAILED->value;
            Cache::put($cacheKey, $order, $this->ttl);
        }
        $this->orderRepository->fail($order_id);
    }

    /**
     * @param int $order_id
     * @return array
     */
    public function getItems(int $order_id): array
    {
        try {
            $cacheKey = self::ORDER_ITEMS_CACHE_PREFIX . $order_id;
            $redis = Redis::connection();
            $items = $redis->smembers($cacheKey);
            $items = array_map(function ($item) {
                return unserialize($item);
            }, $items);
            return $items;
        } catch (\Throwable $e) {}

        $items = $this->orderRepository->getItems($order_id);
            $redis->sadd($cacheKey, ...array_map(function ($item) {
                return serialize($item);
            }, $items));
        $redis->expire($cacheKey, $this->ttl);

        return $items;
    }
}
