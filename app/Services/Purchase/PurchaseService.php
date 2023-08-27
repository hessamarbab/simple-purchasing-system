<?php

namespace App\Services\Purchase;

use App\Driver\Ipg\IpgDriver;
use App\Driver\Ipg\IpgDriverContract;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Exceptions\CustomizedException;
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
     * @param DbTransactionRepositoryContract $dbRepo
     * @param PaymentRepositoryContract $paymentRepo
     * @param ProductRepositoryContract $productRepo
     */
    public function __construct(
        protected DbTransactionRepositoryContract $dbRepo,
        protected OrderRepositoryContract         $orderRepo,
        protected PaymentRepositoryContract       $paymentRepo,
        protected ProductRepositoryContract       $productRepo
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
        $ipgClass = config('ipgs.' . $ipg);
        if ($ipgClass == null) {
            throw new CustomizedException("invalid ipg");
        }
        /** @var IpgDriver $ipgDriver */
        $ipgDriver = app()->makeWith(
            IpgDriverContract::class, [
                'ipgStrategy' => app()->make($ipgClass)
            ]
        );
        $paymentId = $this->reserveInDatabase($user, $items, $ipg);

        return $ipgDriver->generatePaymentUrl($paymentId);
    }

    /**
     * @param array $user
     * @param array $items
     * @param string $ipg
     * @return int
     * @throws Throwable
     */
    private function reserveInDatabase(array $user, array $items, string $ipg): int
    {
        $this->dbRepo->beginTransaction();
        try {
            $order = $this->orderRepo->create($user['id']);
            foreach ($items as $item) {
                $this->productRepo->reduce($item['product_id'], $item['quantity']);
                $this->orderRepo->createItem($order['id'], $user['id'],
                    $item['product_id'], $item['quantity']);
            }
            $amount = $this->calculateAmount($items);
            $paymentId = $this->paymentRepo->create($user['id'], $order['id'], $amount, $ipg)['id'];
            $this->dbRepo->commit();
        } catch (Throwable $e) {
            $this->dbRepo->rollback();
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
        $ipgClass = config('ipgs.' . $bank_kind);
        if ($ipgClass == null) {
            throw new CustomizedException("invalid ipg");
        }
        /** @var IpgDriver $ipgDriver */
        $ipgDriver = app()->makeWith(
            IpgDriverContract::class, [
                'ipgStrategy' => app()->make($ipgClass)
            ]
        );
        $paymentId = $ipgDriver->getPaymentIdByCode($payment_code);
        if ($success) {
            $this->applyInDatabase($paymentId);
        } else {
            $this->cancelInDatabase($paymentId);
        }
    }

    /**
     * @param int $paymentId
     * @return void
     * @throws Throwable
     */
    private function applyInDatabase(int $paymentId)
    {
        $this->dbRepo->beginTransaction();
        try {
            $payment = $this->paymentRepo->getById($paymentId);
            $this->paymentRepo->apply($paymentId);
            $this->orderRepo->apply($payment['order_id']);
            $this->dbRepo->commit();
        } catch (Throwable $e) {
            $this->dbRepo->rollback();
            throw $e;
        }
    }

    /**
     * @param int $paymentId
     * @return void
     * @throws Throwable
     */
    private function cancelInDatabase(int $paymentId)
    {
        $this->dbRepo->beginTransaction();
        try {
            $payment = $this->paymentRepo->getById($paymentId);
            $this->paymentRepo->fail($paymentId);
            $this->orderRepo->fail($payment['order_id']);
            $items = $this->orderRepo->getItems($payment['order_id']);
            foreach ($items as $item) {
                $this->productRepo->enhance($item['product_id'], $item['quantity']);
            }
            $this->dbRepo->commit();
        } catch (Throwable $e) {
            $this->dbRepo->rollback();
            throw $e;
        }
    }
}
