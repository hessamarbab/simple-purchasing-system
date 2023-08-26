<?php

namespace App\Repositories\Order;

use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection;

    public function create(int $user_id): array;

    public function createItem(int $order_id, int $user_id, int $product_id, int $quantity);

    public function apply(int $order_id);

    public function fail(int $order_id);

    public function getItems(int $order_id): array;
}
