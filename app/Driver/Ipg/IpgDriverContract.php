<?php

namespace App\Driver\Ipg;

use App\strategies\Ipg\IpgContract;

interface IpgDriverContract
{
    public function __construct(IpgContract $ipgStrategy);
    public function generatePaymentUrl(int $paymentId);
}
