<?php

namespace App\strategies\Ipg;

class IpgB implements IpgContract
{

    public function getUrlParam()
    {
        return 'ipgb'; //its the same as ipg key in config only use to show strategy pattern for multi ipgs
    }
}
