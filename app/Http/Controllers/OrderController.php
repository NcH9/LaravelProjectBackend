<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountAttachRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Application;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected AuthService $authService,
    ) {}
    public function processPayment(ProcessPaymentRequest $request, Reservation $reservation):JsonResponse
    {
        $validated = $request->validated();

        $order = $reservation->order;

        if ($order->is_paid) {
            return response()->json(['message' => 'Order is already paid'], 400);
        }

        $this->orderService->applyDiscounts($order, [
            "user_id" => $reservation->user_id,
            "price" => $reservation->price,
        ]);


        // The project is not commercial, so payment does not process fully
        $user = User::find($reservation->user_id);
        DB::beginTransaction();
        try {
            $order->is_paid = true;
            $order->save();
            $user->discounts()->attach(Discount::where('loyalty_reward', true)->pluck('id')->toArray());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return response()->json(['message' => 'Order is paid'], 200);

//        $amount = $order->price;
//
//        if (is_null($amount)) {
//            return response()->json(['message' => 'Order not found for this reservation'], 400);
//        }
//
//        $amountInKopecks = intval($amount * 100);
//
//        $session = $this->orderService->processPayment($validated['return_url'], $amountInKopecks, $reservation->id);
//
//        return response()->json(['url' => $session->url]);
    }
    public function attachDiscount(DiscountAttachRequest $request):array {
        $order = Order::find($request['order_id']);
        $user = !empty($request['user_id']) ? User::find($request['user_id']) : Auth::user();
        $discount = $request['discount_id'];

        $this->authService->checkUserDiscount($user, $discount);

        DB::beginTransaction();
        try {
            $order->discounts()->attach($discount);
            $user->discounts()->detach($discount);
            $this->orderService->updatePrice($order);
            DB::commit();
            $response = [
                'order' => $order->load('discounts'),
                'user_discounts' => $user->discounts()->get(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getMessage());
        }

        return $response;
    }
    public function detachDiscount(DiscountAttachRequest $request):array {
        $order = Order::find($request['order_id']);
        $user = !empty($request['user_id']) ? User::find($request['user_id']) : Auth::user();
        $discount = $request['discount_id'];

        $this->orderService->checkOrderDiscount($order, $discount);

        DB::beginTransaction();
        try {
            $order->discounts()->detach($discount);
            $user->discounts()->attach($discount);
            $this->orderService->updatePrice($order);
            DB::commit();
            $response = [
                'order' => $order->load('discounts'),
                'user_discounts' => $user->discounts()->get(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getMessage());
        }

        return $response;
    }
}
