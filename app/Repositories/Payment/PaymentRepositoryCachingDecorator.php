<?php

namespace App\Repositories\Payment;

class PaymentRepositoryCachingDecorator implements PaymentRepositoryContract
{

    /**
     * @param PaymentEloquentRepository $paymentRepository
     */
    public function __construct(
        protected PaymentEloquentRepository $paymentRepository,
        protected int $ttl = 60
    ){}

    public function all()
    {
        // it's not ok to cache all things
        return $this->paymentRepository->all();
    }
}
