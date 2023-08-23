<?php

namespace App\Repositories\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderElequentRepository Implements OrderRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return Order::all();
    }
}
