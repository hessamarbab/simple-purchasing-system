<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatusEnum;

interface PaymentRepositoryContract
{
    public function all();

    public function create(int $user_id, int $order_id, int $amount, string $ipg, PaymentStatusEnum $status);
}
