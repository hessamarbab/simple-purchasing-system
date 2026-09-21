<?php

namespace App\Factories\Ipg;

use App\Driver\Ipg\IpgDriverContract;

interface IpgDriverFactoryContract
{
    public function make(string $ipg): IpgDriverContract;
}
