<?php

namespace App\Repositories\Order;

use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection;

    /**
     * @param int $user_id
     * @return array
     */
    public function create(int $user_id): array;

    /**
     * @param int $order_id
     * @param int $user_id
     * @param int $product_id
     * @param int $quantity
     * @return void
     */
    public function createItem(int $order_id, int $user_id, int $product_id, int $quantity);

    /**
     * @param int $order_id
     * @return void
     */
    public function apply(int $order_id);

    /**
     * @param int $order_id
     * @return void
     */
    public function fail(int $order_id);

    /**
     * @param int $order_id
     * @return array
     */
    public function getItems(int $order_id): array;
}
