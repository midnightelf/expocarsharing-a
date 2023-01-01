<?php

namespace App\Versions\V1\Http\Controllers\Api;

use App\Models\User;
use App\Versions\V1\Http\Controllers\Controller;
use App\Versions\V1\Http\Resources\Order\OrderResource;
use App\Versions\V1\Http\Resources\Order\OrderResourceFactory;
use App\Versions\V1\Http\Resources\UserResource;
use App\Versions\V1\Repositories\OrderRepository;
use App\Versions\V1\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        public UserRepository $repository,
        public OrderRepository $orderRepository,
    ) {
    }

    public function show(Request $request, User $user): UserResource
    {
        return new UserResource($user);
    }

    public function getCurrentUserOrder(Request $request): ?OrderResource
    {
        if (!auth()->hasUser()) {
            throw new AuthenticationException(__('auth.exception.not_set'));
        }

        $order = $this->orderRepository->getOrderByUser(auth()->user());

        return OrderResourceFactory::create($order->status, $order);
    }

    public function fetchProfile(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
