<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Repositories\Order\OrderRepositoryBuilder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        public OrderRepositoryBuilder $orderRepositoryBuilder
    ) {}

    public function all()
    {
        return $this->orderRepositoryBuilder->build()->all();
    }


}
