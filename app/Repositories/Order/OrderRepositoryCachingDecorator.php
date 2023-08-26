<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Collection;

class OrderRepositoryCachingDecorator implements OrderRepositoryContract
{

    /**
     * @param OrderRepositoryContract $orderRepository
     * @param int $ttl
     */
    public function __construct(
        protected int $ttl = 60,
        protected OrderRepositoryContract $orderRepository = new OrderEloquentRepository()
    ) {}

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        // it's not ok to cache all things
        return $this->orderRepository->all();
    }

    public function create(int $user_id, OrderStatusEnum $status)
    {
        // TODO: Implement create() method.
        return $this->orderRepository->create($user_id, $status);
    }

    public function createItem(int $order_id, int $user_id, int $product_id, int $quantity)
    {
        // TODO: Implement createItem() method.
        return $this->orderRepository->createItem($order_id,  $user_id,  $product_id,  $quantity);
    }
}
