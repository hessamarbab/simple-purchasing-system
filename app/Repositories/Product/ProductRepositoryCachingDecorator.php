<?php

namespace App\Repositories\Product;

class ProductRepositoryCachingDecorator implements ProductRepositoryContract
{

    /**
     * @param ProductEloquentRepository $productRepository
     */
    public function __construct(
        protected ProductEloquentRepository $productRepository,
        protected int $ttl = 60
    ){}

    public function all()
    {
        // it's not ok to cache all things
        return $this->productRepository->all();
    }
}
