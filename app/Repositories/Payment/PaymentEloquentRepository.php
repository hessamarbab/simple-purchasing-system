<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class PaymentEloquentRepository implements PaymentRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return Payment::all();
    }

    public function create(int $user_id, int $order_id, int $amount, string $ipg): array
    {
        Payment::unguard();
        $out = Payment::create([
            'user_id' => $user_id,
            'order_id' => $order_id,
            'status' => PaymentStatusEnum::PENDING,
            'amount' => $amount,
            'gateway_type' => $ipg
        ])->toArray();
        Payment::reguard();
        return $out;
    }

    public function apply(int $paymentId)
    {
        Payment::where('id', $paymentId)->update([
                'status' => PaymentStatusEnum::COMPLETED,
                'paid_at' => Carbon::now()
            ]);
    }

    public function fail(int $paymentId)
    {
        Payment::where('id', $paymentId)->update(['status' => PaymentStatusEnum::CANCELED]);
    }

    /**
     * @param int $paymentId
     * @return array
     */
    public function getById(int $paymentId): array
    {
        return Payment::find($paymentId)->toArray();
    }
}
