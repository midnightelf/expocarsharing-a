<?php

namespace App\Versions\V1\Http\Controllers\Api;

use App\Models\Offer;
use App\Models\Order;
use App\Versions\V1\DTO\OrderDto;
use App\Versions\V1\Http\Controllers\Controller;
use App\Versions\V1\Http\Resources\Collections\Order\OrderConfirmingCollection;
use App\Versions\V1\Http\Resources\Collections\Order\OrderRentedCollection;
use App\Versions\V1\Http\Resources\Collections\Order\OrderReservedCollection;
use App\Versions\V1\Http\Resources\Order\OrderConfirmingResource;
use App\Versions\V1\Http\Resources\Order\OrderRentedResource;
use App\Versions\V1\Http\Resources\Order\OrderReservedResource;
use App\Versions\V1\Http\Resources\Order\OrderFinishResource;
use App\Versions\V1\Http\Resources\Order\OrderResource;
use App\Versions\V1\Repositories\OfferRepository;
use App\Versions\V1\Repositories\OrderRepository;
use App\Versions\V1\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        public OrderService $service
    ) {
    }

    public function show(Request $request, Order $order): OrderResource
    {
        return new OrderResource($order);
    }

    public function reserv(Request $request, Offer $offer)
    {
        /** @var OfferRepository $offer */
        $offer = app(OfferRepository::class, ['offer' => $offer]);
        $order = null;

        DB::transaction(function () use ($request, $offer, &$order) {
            $offer->makeUnavailable();

            $order = $this->service->store(OrderDto::fromRequest($request));
        });

        return new OrderReservedResource($order);
    }

    public function confirmRent(Request $request, Order $order)
    {
        /** @var OrderRepository $order */
        $order = app(OrderRepository::class, ['order' => $order]);
        $order->updateStatus(Order::STATUS_CONFIRMING_RENT)->save();

        return new OrderConfirmingResource($order->getOrder());
    }

    public function confirmPayment(Request $request, Order $order)
    {
        /** @var OrderRepository $order */
        $order = app(OrderRepository::class, ['order' => $order]);

        $order->updateStatus(Order::STATUS_CONFIRMING_PAYMENT)->save();

        return new OrderConfirmingResource($order->getOrder());
    }

    public function rent(Request $request, Order $order)
    {
        /** @var OrderRepository $order */
        $order = app(OrderRepository::class, ['order' => $order]);

        DB::transaction(function () use ($order) {
            $order->startRent();
            $order->updateStatus(Order::STATUS_RENTED)->save();
        });

        return new OrderRentedResource($order->getOrder());
    }

    public function finish(Request $request, Order $order)
    {
        $offer = $order->offer;

        /** @var OrderRepository $order */
        $order = app(OrderRepository::class, ['order' => $order]);
        /** @var OfferRepository $offer */
        $offer = app(OfferRepository::class, ['offer' => $offer]);

        // DB::transaction(function () use ($order, $offer) {
        //     $order->finishRent()->save();
        //     $order->delete();

        //     $offer->makeAvailable();
        // });

        return new OrderFinishResource($order->getOrder());
    }

    public function reserved(Request $request): OrderReservedCollection
    {
        $orders = app(OrderRepository::class)->getByStatus(Order::STATUS_RESERVED);

        return new OrderReservedCollection($orders);
    }

    public function confirming(Request $request): OrderConfirmingCollection
    {
        $orders = app(OrderRepository::class)->getByStatus(Order::STATUS_CONFIRMING_RENT);

        return new OrderConfirmingCollection($orders);
    }

    public function rented(Request $request): OrderRentedCollection
    {
        $orders = app(OrderRepository::class)->getByStatus(Order::STATUS_RENTED);

        return new OrderRentedCollection($orders);
    }
}
