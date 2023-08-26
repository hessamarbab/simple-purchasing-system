<?php

namespace App\Repositories\Product;

use App\Exceptions\CustomizedException;
use App\Models\Product;

class ProductEloquentRepository implements ProductRepositoryContract
{
    public function all()
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

    public function getById(int $id): array
    {
        return Product::find($id)->toArray();
    }

    public function enhance(int $product_id, int $quantity)
    {
        Product::where(['id' => $product_id])->increment('inventory', $quantity);
    }
}
