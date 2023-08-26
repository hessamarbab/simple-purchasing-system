<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepositoryCachingDecorator implements PaymentRepositoryContract
{

    /**
     * @param PaymentRepositoryContract $paymentRepository
     */
    public function __construct(
        protected int $ttl = 60,
        protected PaymentRepositoryContract $paymentRepository = new PaymentEloquentRepository()
    ){}

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        // it's not ok to cache all things
        return $this->paymentRepository->all();
    }

    /**
     * @param int $user_id
     * @param int $order_id
     * @param int $amount
     * @param string $ipg
     * @return array
     */
    public function create(int $user_id, int $order_id, int $amount, string $ipg): array
    {
        // TODO: Implement create() method.
        return $this->paymentRepository->create($user_id, $order_id, $amount, $ipg);
    }

    public function fail(int $paymentId)
    {
        // TODO: Implement fail() method.
        $this->paymentRepository->fail($paymentId);
    }

    public function apply(int $paymentId)
    {
        // TODO: Implement apply() method.
        $this->paymentRepository->apply($paymentId);
    }

    public function getById(int $paymentId): array
    {
        // TODO: Implement getById() method.
        return $this->paymentRepository->getById($paymentId);
    }
}
