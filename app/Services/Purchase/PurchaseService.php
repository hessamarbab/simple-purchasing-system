<?php

namespace App\Services\Purchase;

use App\Driver\Ipg\IpgDriver;
use App\Factories\Ipg\IpgDriverFactoryContract;
use App\Repositories\Atomic\DbTransactionRepositoryContract;
use App\Repositories\Order\OrderRepositoryContract;
use App\Repositories\Payment\PaymentRepositoryContract;
use App\Repositories\Product\ProductRepositoryContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Throwable;

class PurchaseService implements PurchaseServiceContract
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
        protected IpgDriverFactoryContract $ipgDriverFactory
    )
    {
    }

    /**
     * @param array $user
     * @param array $items
     * @return string
     * @throws Throwable
     */
    public function reserve(array $user, array $items, string $ipg): string
    {
        $paymentId = $this->reserveOrder(
            user: $user,
            items: $items,
            ipg: $ipg
        );

        $ipgDriver = $this->ipgDriverFactory->make($ipg);
        return $ipgDriver->generatePaymentUrl($paymentId);
    }



    /**
     * @param array $user
     * @param array $items
     * @param string $ipg
     * @return int
     * @throws Throwable
     */
    private function reserveOrder(array $user, array $items, string $ipg): int
    {
        $this->atomicRepo->beginTransaction();
        try {
            $order = $this->orderRepo->create($user['id']);
            $amount = $this->calculateAmount($items);

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

    /**
     * @param array $items
     * @return int
     */
    private function calculateAmount(array $items): int
    {
        $amount = 0;
        foreach ($items as $item) {
            $product = $this->productRepo->getById($item['product_id']);
            $amount += $item['quantity'] * $product['price'];
        }
        return $amount;
    }

    /**
     * @param string $bank_kind
     * @param string $payment_code
     * @param bool $success
     * @return void
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function confirm(string $bank_kind, string $payment_code, bool $success)
    {
        /** @var IpgDriver $ipgDriver */
        $ipgDriver = $this->ipgDriverFactory->make($bank_kind);
        $paymentId = $ipgDriver->getPaymentIdByCode($payment_code);

        $success
            ? $this->applyPayment($paymentId)
            : $this->cancelPayment($paymentId);
    }

    /**
     * @param int $paymentId
     * @return void
     * @throws Throwable
     */
    private function applyPayment(int $paymentId): void
    {
        $this->atomicRepo->beginTransaction();
        try {
            $payment = $this->paymentRepo->getById($paymentId);
            $this->paymentRepo->apply($paymentId);
            $this->orderRepo->apply($payment['order_id']);
            $this->atomicRepo->commit();
        } catch (Throwable $e) {
            $this->atomicRepo->rollback();
            throw $e;
        }
    }

    /**
     * @param int $paymentId
     * @return void
     * @throws Throwable
     */
    private function cancelPayment(int $paymentId): void
    {
        $this->atomicRepo->beginTransaction();
        try {
            $payment = $this->paymentRepo->getById($paymentId);
            $this->paymentRepo->fail($paymentId);
            $this->orderRepo->fail($payment['order_id']);
            $items = $this->orderRepo->getItems($payment['order_id']);
            foreach ($items as $item) {
                $this->productRepo->enhance($item['product_id'], $item['quantity']);
            }
            $this->atomicRepo->commit();
        } catch (Throwable $e) {
            $this->atomicRepo->rollback();
            throw $e;
        }
    }
}
