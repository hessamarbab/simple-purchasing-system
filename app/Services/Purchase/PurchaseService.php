<?php

namespace App\Services\Purchase;

use App\Driver\Ipg\IpgDriver;
use App\Driver\Ipg\IpgDriverContract;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Repositories\Atomic\DbTransactionRepositoryContract;
use App\Repositories\Order\OrderRepositoryContract;
use App\Repositories\Payment\PaymentRepositoryContract;
use App\Repositories\Product\ProductRepositoryContract;
use Throwable;

class PurchaseService implements PurchaseServiceContract
{
    /**
     * @param OrderRepositoryContract $orderRepo
     * @param DbTransactionRepositoryContract $dbRepo
     * @param PaymentRepositoryContract $paymentRepo
     * @param ProductRepositoryContract $productRepo
     */
    public function __construct(
        protected DbTransactionRepositoryContract $dbRepo,
        protected OrderRepositoryContract $orderRepo,
        protected PaymentRepositoryContract $paymentRepo,
        protected ProductRepositoryContract $productRepo
    ) {}

    /**
     * @param array $user
     * @param array $items
     * @return string
     * @throws Throwable
     */
    public function reserve(array $user, array $items, string $ipg): string
    {
        /** @var IpgDriver $ipgDriver */
        $ipgDriver = app()->makeWith(
            IpgDriverContract::class, [
                'ipgStrategy' => app()->make(config('ipgs.'.$ipg))
            ]
        );
        $this->dbRepo->beginTransaction();
        try {
            $order = $this->orderRepo->create($user['id'], OrderStatusEnum::RESERVED);
            foreach ($items as $item)
            {
                $this->productRepo->reduce($item['product_id'], $item['quantity']);
                $this->orderRepo->createItem($order['id'], $user['id'], $item['product_id'], $item['quantity']);
            }
            $amount = $this->calculateAmount($items);
            $paymentId = $this->paymentRepo->create($user['id'], $order['id'], $amount, $ipg,PaymentStatusEnum::PENDING)->id;
            $this->dbRepo->commit();
        } catch (Throwable $e) {
            $this->dbRepo->rollback();
            throw $e;
        }
        return $ipgDriver->generatePaymentUrl($paymentId);
    }

    public function confirm(string $bank_kind, string $payment_code, bool $success)
    {
       // if ()
    }

    private function calculateAmount(array $items)
    {
        $amount = 0;
        foreach ($items as $item) {
            $product = $this->productRepo->getById($item['product_id']);
            $amount += $item['quantity'] * $product['price'];
        }
        return $amount;
    }


}
