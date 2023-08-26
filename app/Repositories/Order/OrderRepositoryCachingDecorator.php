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

    public function create(int $user_id): array
    {
        // TODO: Implement create() method.
        return $this->orderRepository->create($user_id);
    }

    public function createItem(int $order_id, int $user_id, int $product_id, int $quantity)
    {
        // TODO: Implement createItem() method.
        $this->orderRepository->createItem($order_id, $user_id, $product_id, $quantity);
    }

    public function apply(int $order_id)
    {
        // TODO: Implement apply() method.
        $this->orderRepository->apply($order_id);
    }

    public function fail(int $order_id)
    {
        // TODO: Implement fail() method.
        $this->orderRepository->fail($order_id);
    }

    public function getItems(int $order_id): array
    {
        // TODO: Implement getItems() method.
        return $this->orderRepository->getItems($order_id);
    }
}
