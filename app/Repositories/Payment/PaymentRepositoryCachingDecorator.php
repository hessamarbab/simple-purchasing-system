<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatusEnum;
use App\Exceptions\CustomizedException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PaymentRepositoryCachingDecorator implements PaymentRepositoryContract
{
    const PAYMENT_CACHE_PREFIX = 'PAYMENT_';

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
        $payment = $this->paymentRepository->create($user_id, $order_id, $amount, $ipg);
        $cacheKey = self::PAYMENT_CACHE_PREFIX . $payment['id'];
        Cache::put($cacheKey, $payment, $this->ttl);
        return $payment;
    }

    /**
     * @param int $paymentId
     * @return void
     * @throws CustomizedException
     */
    public function fail(int $paymentId)
    {
        $cacheKey = self::PAYMENT_CACHE_PREFIX . $paymentId;
        $payment = Cache::get($cacheKey);
        if ($payment != null) {
            if ($payment['status'] != PaymentStatusEnum::PENDING->value) {
                throw new CustomizedException("only one time you can call confirm page");
            }
            $payment['status'] = PaymentStatusEnum::CANCELED->value;
            Cache::put($cacheKey, $payment, $this->ttl);
        }
        $this->paymentRepository->fail($paymentId);
    }

    /**
     * @param int $paymentId
     * @return void
     * @throws CustomizedException
     */
    public function apply(int $paymentId)
    {
        $cacheKey = self::PAYMENT_CACHE_PREFIX . $paymentId;
        $payment = Cache::get($cacheKey);
        if ($payment != null) {
            if ($payment['status'] != PaymentStatusEnum::PENDING->value) {
                throw new CustomizedException("only one time you can call confirm page");
            }
            $payment['status'] = PaymentStatusEnum::COMPLETED->value;
            $payment['paid_at'] = Carbon::now();
            Cache::put($cacheKey, $payment, $this->ttl);
        }
        $this->paymentRepository->apply($paymentId);
    }

    /**
     * @param int $paymentId
     * @return array
     */
    public function getById(int $paymentId): array
    {
        $cacheKey = self::PAYMENT_CACHE_PREFIX . $paymentId;
        return Cache::remember(
            $cacheKey,
            $this->ttl,
            function () use ($paymentId) {
                return $this->paymentRepository->getById($paymentId);
            });
    }
}
