<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatusEnum;
use App\Exceptions\CustomizedException;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class PaymentEloquentRepository implements PaymentRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return Payment::all();
    }

    /**
     * @param int $user_id
     * @param int $order_id
     * @param int $amount
     * @param string $ipg
     * @return array
     */
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

    /**
     * @param int $paymentId
     * @return void
     * @throws CustomizedException
     */
    public function apply(int $paymentId)
    {
        if(!Payment::where('id', $paymentId)
            ->where('status', '=', PaymentStatusEnum::PENDING) //cause should work only one time
            ->update([
                'status' => PaymentStatusEnum::COMPLETED,
                'paid_at' => Carbon::now()
            ])) {
                throw new CustomizedException("only one time you can call confirm page");
        }
    }

    /**
     * @param int $paymentId
     * @return void
     * @throws CustomizedException
     */
    public function fail(int $paymentId)
    {
        if(!Payment::where('id', $paymentId)
            ->where('status', '=', PaymentStatusEnum::PENDING) //cause should work only one time
            ->update(['status' => PaymentStatusEnum::CANCELED])) {
                throw new CustomizedException("only one time you can call confirm page");
        }
    }

    /**
     * @param int $paymentId
     * @return array
     */
    public function getById(int $paymentId): array
    {
        return Payment::findOrFail($paymentId)->toArray();
    }
}
