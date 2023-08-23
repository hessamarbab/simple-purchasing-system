<?php

namespace App\Repositories\Order;

use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryContract
{
    /**
     * @return Collection
     */
    public function all() : Collection;
}
