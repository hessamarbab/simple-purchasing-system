<?php

namespace App\Repositories\Order;

use Illuminate\Database\Eloquent\Collection;

class OrderRepositoryCachingDecorator implements OrderRepositoryContract
{

    /**
     * @param OrderElequentRepository $orderRepository
     * @param int $ttl
     */
    public function __construct(
        protected OrderElequentRepository $orderRepository,
        int $ttl
    ) {}

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        // it's not ok to cache all things ever !!!
        return $this->orderRepository->all();
    }
}
