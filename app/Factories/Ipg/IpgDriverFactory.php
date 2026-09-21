<?php

namespace App\Factories\Ipg;

use App\Driver\Ipg\IpgDriver;
use App\Driver\Ipg\IpgDriverContract;
use App\Exceptions\InvalidPaymentGatewayException;

class IpgDriverFactory implements IpgDriverFactoryContract
{
    public function make(string $ipg): IpgDriverContract
    {
        $ipgClass = config('ipgs.' . $ipg);
        if ($ipgClass == null) {
            throw new InvalidPaymentGatewayException("invalid ipg");
        }
        /** @var IpgDriver $ipgDriver */
        $ipgDriver = app()->makeWith(
            IpgDriverContract::class, [
                'ipgStrategy' => app()->make($ipgClass)
            ]
        );
        return $ipgDriver;
    }
}
