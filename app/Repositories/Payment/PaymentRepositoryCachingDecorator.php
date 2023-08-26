<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatusEnum;

class PaymentRepositoryCachingDecorator implements PaymentRepositoryContract
{

    /**
     * @param PaymentRepositoryContract $paymentRepository
     */
    public function __construct(
        protected int $ttl = 60,
        protected PaymentRepositoryContract $paymentRepository = new PaymentEloquentRepository()
    ){}

    public function all()
    {
        // it's not ok to cache all things
        return $this->paymentRepository->all();
    }

    public function create(int $user_id, int $order_id, int $amount, string $ipg, PaymentStatusEnum $status)
    {
        // TODO: Implement create() method.
        return $this->paymentRepository->create($user_id, $order_id, $amount, $ipg, $status);
    }
}
