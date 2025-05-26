<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountAttachRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected AuthService $authService,
    ) {}
    public function processPayment($request) {

    }
    public function attachDiscount(DiscountAttachRequest $request) {
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
    public function detachDiscount(DiscountAttachRequest $request) {
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
