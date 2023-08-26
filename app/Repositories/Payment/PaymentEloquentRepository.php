<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;

class PaymentEloquentRepository implements PaymentRepositoryContract
{
    public function all()
    {
        return Payment::all();
    }

    public function create(int $user_id, int $order_id, int $amount, string $ipg, PaymentStatusEnum $status)
    {
        Payment::unguard();
        $out = Payment::create([
            'user_id' => $user_id,
            'order_id' => $order_id,
            'status' => $status,
            'amount' => $amount,
            'gateway_type' => $ipg
        ]);
        Payment::reguard();
        return $out;
    }
}
