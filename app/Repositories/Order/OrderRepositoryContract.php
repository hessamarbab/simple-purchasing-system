<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryContract
{
    /**
     * @return Collection
     */
    public function all() : Collection;

    public function create(int $user_id, OrderStatusEnum $status);

    public function createItem(int $order_id, int $user_id, int $product_id, int $quantity);
}
