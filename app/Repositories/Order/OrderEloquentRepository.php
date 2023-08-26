<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class OrderEloquentRepository implements OrderRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return Order::with('orderItems')->get();
    }

    /**
     * @param int $user_id
     * @return array
     */
    public function create(int $user_id): array
    {
        Order::unguard();
        $out = Order::create([
            'user_id' => $user_id,
            'status' => OrderStatusEnum::RESERVED,
            'reserved_at' => Carbon::now()
        ])->toArray();
        Order::reguard();
        return $out;
    }

    /**
     * @param int $order_id
     * @param int $user_id
     * @param int $product_id
     * @param int $quantity
     * @return void
     */
    public function createItem(int $order_id, int $user_id, int $product_id, int $quantity)
    {
        OrderItem::unguard();
        OrderItem::create([
            'order_id' => $order_id,
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity
        ]);
        OrderItem::reguard();
    }

    public function apply(int $order_id)
    {
        Order::where('id', $order_id)->update(['status' => OrderStatusEnum::PERFORMED]);
    }

    public function fail(int $order_id)
    {
        Order::where('id', $order_id)->update(['status' => OrderStatusEnum::FAILED]);
    }


    public function getItems(int $order_id): array
    {
        // TODO: Implement getItems() method.
        return OrderItem::query()->select('product_id', 'quantity')
                ->where('order_id', $order_id)->get()->toArray();
    }
}
