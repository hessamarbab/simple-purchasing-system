<?php

namespace App\Repositories\Order;

class OrderRepositoryBuilder
{
    private bool $useCache = false;

    public function __construct(
        private OrderRepositoryContract $orderRepository = new OrderEloquentRepository()
    )
    {
    }

    public function useCache(int $ttl)
    {
        if($this->useCache) {
            return $this;
        }
        $this->orderRepository = new OrderRepositoryCachingDecorator($this->orderRepository, $ttl);
        $this->useCache = true;
        return $this;
    }

    /**
     * @return OrderRepositoryContract
     */
    public function build(): OrderRepositoryContract
    {
        return $this->orderRepository;
    }
}
