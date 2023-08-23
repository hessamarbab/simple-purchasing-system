<?php

namespace App\Repositories\Order;

use App\Repositories\Order\OrderElequentRepository;

class OrderRepositoryBuilder
{
    private $ttl = 0;

    public function __construct(
        private OrderElequentRepository $orderRepository
    ) {}

    public function useCache(int $ttl)
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * @return OrderRepositoryContract
     */
    public function build() : OrderRepositoryContract
    {
        if ($this->ttl != 0) {
            return new OrderRepositoryCachingDecorator($this->orderRepository, $this->ttl);
        } else {
            return $this->orderRepository;
        }
    }
}
