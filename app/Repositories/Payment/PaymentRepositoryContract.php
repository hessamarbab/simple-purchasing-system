<?php

namespace App\Repositories\Payment;


use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryContract
{
    /**
     * @return Collection
     */
    public function all(): Collection;

    /**
     * @param int $user_id
     * @param int $order_id
     * @param int $amount
     * @param string $ipg
     * @return array
     */
    public function create(int $user_id, int $order_id, int $amount, string $ipg): array;

    /**
     * @param int $paymentId
     * @return void
     */
    public function fail(int $paymentId);

    /**
     * @param int $paymentId
     * @return void
     */
    public function apply(int $paymentId);


    /**
     * @param int $paymentId
     * @return array
     */
    public function getById(int $paymentId): array;
}
