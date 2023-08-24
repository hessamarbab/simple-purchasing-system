<?php

namespace App\Repositories\Product;

use App\Models\Product;

class ProductEloquentRepository implements ProductRepositoryContract
{
    public function all()
    {
        return Product::all();
    }
}
