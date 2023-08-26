<?php

namespace App\Repositories\Product;

interface ProductRepositoryContract
{
    public function all();

    public function reduce(int $product_id, int $quantity);

    public function getById(int $id): array;

    public function enhance(int $product_id, int $quantity);
}
