<?php

namespace App\Services\Calculator;

use App\Repositories\Product\ProductRepositoryContract;

interface CalculatorServiceContract
{
    public function calculate(array $items): int;
}
