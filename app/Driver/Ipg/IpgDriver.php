<?php

namespace App\Driver\Ipg;

use App\strategies\Ipg\IpgContract;

class IpgDriver implements IpgDriverContract
{

    public function __construct(
        public IpgContract $ipgStrategy
    ) {}

    public function generatePaymentUrl(int $paymentId)
    {
        return route('return-bank', [
            'bank_kind' => $this->ipgStrategy->getUrlParam(),// just for show how has more than one ipg
            'payment_code' => base64_encode($paymentId)
        ]);
    }
}
