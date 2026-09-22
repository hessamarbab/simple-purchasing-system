<?php

namespace App\Services\reserve;

use App\Repositories\Atomic\DbTransactionRepositoryContract;
use App\Repositories\Order\OrderRepositoryContract;
use App\Repositories\Payment\PaymentRepositoryContract;
use App\Repositories\Product\ProductRepositoryContract;
use App\Services\Calculator\CalculatorServiceContract;
use Throwable;

class OrderReserveService implements OrderReserveServiceContract
{
    /**
     * @param OrderRepositoryContract $orderRepo
     * @param DbTransactionRepositoryContract $atomicRepo
     * @param PaymentRepositoryContract $paymentRepo
     * @param ProductRepositoryContract $productRepo
     */
    public function __construct(
        protected DbTransactionRepositoryContract $atomicRepo,
        protected OrderRepositoryContract         $orderRepo,
        protected PaymentRepositoryContract       $paymentRepo,
        protected ProductRepositoryContract       $productRepo,
        protected CalculatorServiceContract $calcService
    )
    {
    }
    /**
     * @param array $user
     * @param array $items
     * @param string $ipg
     * @return int
     * @throws Throwable
     */
    public function reserveOrder(array $user, array $items, string $ipg): int
    {
        $this->atomicRepo->beginTransaction();
        try {
            $order = $this->orderRepo->create($user['id']);
            $amount = $this->calcService->calculate($items);

            foreach ($items as $item) {
                $this->productRepo->reduce($item['product_id'], $item['quantity']);
                $this->orderRepo->createItem($order['id'], $user['id'],
                    $item['product_id'], $item['quantity']);
            }
            $paymentId = $this->paymentRepo->create($user['id'], $order['id'], $amount, $ipg)['id'];
            $this->atomicRepo->commit();
        } catch (Throwable $e) {
            $this->atomicRepo->rollback();
            throw $e;
        }
        return $paymentId;
    }

}
