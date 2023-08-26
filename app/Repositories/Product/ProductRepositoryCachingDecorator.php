<?php

namespace App\Repositories\Product;

class ProductRepositoryCachingDecorator implements ProductRepositoryContract
{

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
        // TODO: Implement reduce() method.
        $this->productRepository->reduce($product_id, $quantity);
    }

    public function getById(int $id): array
    {
        // TODO: Implement getById() method.
        return $this->productRepository->getById($id);
    }

    public function enhance(int $product_id, int $quantity)
    {
        // TODO: Implement enhance() method.
        $this->productRepository->enhance($product_id, $quantity);
    }
}
