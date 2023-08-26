<?php

namespace App\Repositories\Product;

use App\Models\Product;

class ProductEloquentRepository implements ProductRepositoryContract
{
    public function all()
    {
        return Product::all();
    }

    public function reduce(int $product_id, int $quantity)
    {
        //todo return a exception if not enough inventory
        Product::where(['id' => $product_id])->where('inventory', '>=', $quantity)->decrement('inventory', $quantity);
    }

    public function getById(int $id)
    {
        return Product::find($id);
    }
}
