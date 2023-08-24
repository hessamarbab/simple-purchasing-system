<?php

namespace App\Repositories\Order;

use Illuminate\Database\Eloquent\Collection;

class OrderRepositoryCachingDecorator implements OrderRepositoryContract
{

    /**
     * @param OrderEloquentRepository $orderRepository
     * @param int $ttl
     */
    public function __construct(
        protected OrderEloquentRepository $orderRepository,
        protected int $ttl = 60
    ) {}

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        // it's not ok to cache all things
        return $this->orderRepository->all();
    }
}
