<?php

namespace App\Repositories\Payment;


use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryContract
{
    public function all(): Collection;

    public function create(int $user_id, int $order_id, int $amount, string $ipg): array;

    public function fail(int $paymentId);

    public function apply(int $paymentId);

    public function getById(int $paymentId): array;
}
