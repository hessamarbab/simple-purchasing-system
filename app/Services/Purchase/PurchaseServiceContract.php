<?php

namespace App\Services\Purchase;

interface PurchaseServiceContract
{
    /**
     * @param array $user
     * @param array $items
     * @param string $ipg
     * @return string
     */
    public function reserve(array $user, array $items, string $ipg) : string;

    /**
     * @param string $bank_kind
     * @param string $payment_code
     * @param bool $success
     * @return void
     */
    public function confirm(string $bank_kind, string $payment_code, bool $success);
}
