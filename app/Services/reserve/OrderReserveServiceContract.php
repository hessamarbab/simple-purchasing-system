<?php

namespace App\Services\reserve;

interface OrderReserveServiceContract
{
    public function reserveOrder(array $user, array $items, string $ipg): int;
}
