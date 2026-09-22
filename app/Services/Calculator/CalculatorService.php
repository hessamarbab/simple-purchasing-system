<?php

namespace App\Services\Calculator;

use App\Repositories\Product\ProductRepositoryContract;

class CalculatorService implements CalculatorServiceContract
{
    public function __construct(
        protected ProductRepositoryContract   $productRepo,
    ){}


    public function calculate(array $items): int
    {
        $amount = 0;
        $products = $this->productRepo->getByIds(array_column($items, "product_id"));
        foreach ($items as $item) {
            $amount += $item['quantity'] * $products[$item["product_id"]]['price'];
        }
        return $amount;
    }
}
