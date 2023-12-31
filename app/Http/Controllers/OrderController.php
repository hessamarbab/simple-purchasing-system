<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderConfirmRequest;
use App\Http\Requests\OrderReserveRequest;
use App\Repositories\Order\OrderRepositoryCachingDecorator;
use App\Repositories\Order\OrderRepositoryContract;
use App\Repositories\User\UserRepositoryCachingDecorator;
use App\Repositories\User\UserRepositoryContract;
use App\Services\Purchase\PurchaseService;
use App\Services\Purchase\PurchaseServiceContract;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class OrderController extends Controller
{

    /**
     * @return \Illuminate\Foundation\Application|Response|Application|ResponseFactory
     * @throws BindingResolutionException
     */
    public function all(): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        /** @var OrderRepositoryCachingDecorator $orderRepositoryBuilder */
        $orderRepositoryBuilder = app()->make(OrderRepositoryContract::class);
        return response($orderRepositoryBuilder->all(), 200);
    }

    /**
     * @param OrderReserveRequest $request
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws AuthenticationException
     * @throws BindingResolutionException
     */
    public function reserve(OrderReserveRequest $request): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        /** @var PurchaseService $purchaseService */
        $purchaseService = app()->make(PurchaseServiceContract::class);

        /** @var UserRepositoryCachingDecorator $userRepository */
        $userRepository = app()->make(UserRepositoryContract::class);


        $user = $userRepository->getByUsername($request->input('username'));
        if($user['id'] != $request->input('user_id')) {
            throw new AuthenticationException();
        }
        $ipg = $request->input('ipg');
        $items = $request->input('items');
        $bankUrl = $purchaseService->reserve($user, $items, $ipg);
        $response = [
            'message' => "successfully reserved for confirm your purchase use bank urls " .
                ": success if paid , failed if not paid",
            'success_url' => $bankUrl . "?success=1",
            'failed_url' => $bankUrl . "?success=0",
        ];
        return response(json_encode($response), 200);
    }

    /**
     * @param $bank_kind
     * @param $payment_code
     * @param OrderConfirmRequest $request
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws BindingResolutionException
     * @throws \Throwable
     */
    public function confirm($bank_kind, $payment_code, OrderConfirmRequest $request)
    {
        /** @var PurchaseService $purchaseService */
        $purchaseService = app()->make(PurchaseServiceContract::class);

        $purchaseService->confirm($bank_kind, $payment_code, $request->input('success'));

        return response(null, 201);
    }
}
