<?php

namespace App\Repositories\Product;

use App\Exceptions\CustomizedException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductEloquentRepository implements ProductRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return Product::all();
    }

    /**
     * @throws CustomizedException
     */
    public function reduce(int $product_id, int $quantity)
    {
        if(
            !Product::where(['id' => $product_id])->where('inventory', '>=', $quantity)
                ->decrement('inventory', $quantity)
        ) {
            throw new CustomizedException("there isn't enough inventory");
        }
    }

    /**
     * @param int $id
     * @return array
     */
    public function getById(int $id): array
    {
        return Product::find($id)->toArray();
    }

    /**
     * @param int $product_id
     * @param int $quantity
     * @return void
     */
    public function enhance(int $product_id, int $quantity)
    {
        Product::where(['id' => $product_id])->increment('inventory', $quantity);
    }
}
