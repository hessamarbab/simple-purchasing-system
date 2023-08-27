<?php

namespace App\Repositories\Product;

use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection;

    /**
     * @param int $product_id
     * @param int $quantity
     * @return void
     */
    public function reduce(int $product_id, int $quantity);

    /**
     * @param int $id
     * @return array
     */
    public function getById(int $id): array;

    /**
     * @param int $product_id
     * @param int $quantity
     * @return void
     */
    public function enhance(int $product_id, int $quantity);
}
