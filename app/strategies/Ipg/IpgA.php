<?php

namespace App\strategies\Ipg;

class IpgA implements IpgContract
{

    public function getUrlParam()
    {
        return 'ipga';//it's the same as ipg key in config only use to show strategy pattern for multi ipgs
    }
}
