<?php

namespace App\Services\Purchase;

interface PurchaseServiceContract
{
    public function reserve(array $user, array $items, string $ipg) : string;

    public function confirm(string $bank_kind, string $payment_code,bool $success);
}
