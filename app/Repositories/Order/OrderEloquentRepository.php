<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class OrderEloquentRepository Implements OrderRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return Order::with('orderItems')->get();
    }

    public function create(int $user_id, OrderStatusEnum $status)
    {
        Order::unguard();
        $orderData = [
            'user_id' => $user_id,
            'status' => $status
        ];
        if($status == OrderStatusEnum::RESERVED) {
            $orderData['reserved_at'] = Carbon::now();
        }
        $out = Order::create($orderData);
        Order::reguard();
        return $out;
    }

    public function createItem(int $order_id, int $user_id, int $product_id, int $quantity)
    {
        OrderItem::unguard();
        $out = OrderItem::create([
            'order_id' => $order_id,
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity
        ]);
        OrderItem::reguard();
        return $out;
    }
}
