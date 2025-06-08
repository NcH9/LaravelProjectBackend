<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class OrderService
{
    public function create(int $reservationId, array $data) {
        $order = Order::create([
            'user_id' => $data['user_id'],
            'reservation_id' => $reservationId,
            'price' => $data['price'],
            'is_paid' => false,
        ]);

        $this->applyDiscounts($order, $data);
    }
    public function applyDiscounts(Order $order, array $data) {
        $seasonalDiscounts = Discount::where('is_seasonal', true)->pluck('id');
        $missingDiscounts = $seasonalDiscounts->diff($order->discounts->pluck('id'));
        if ($missingDiscounts->isNotEmpty()) {
            $order->discounts()->syncWithoutDetaching($missingDiscounts);
        }

        $user = User::find($data['user_id']);
        $discountId = $user->discounts()->pluck('discounts.id')->first();

        if (!$order->discounts->contains($discountId)) {
            $order->discounts()->attach($discountId);
            $user->discounts()->detach($discountId);
        }

        $newPrice = $this->countDiscountedOrderPrice($order, $data['price']);
        $order->price = $newPrice;
        $order->save();
    }
    private function countDiscountedOrderPrice(Order $order, float $price) {
        $priceReducedPercent = 0;

        foreach ($order->discounts()->get() as $discount) {
            $priceReducedPercent += $discount->percent;
        }

        if ($priceReducedPercent > 50) {
            return $price/2;
        }

        return ($price*(100-$priceReducedPercent))/100;
    }
    public function processPayment(string $url, float $price, int $reservationId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'uah',
                    'unit_amount' => $price,
                    'product_data' => [
                        'name' => 'Payment of reservation №' . $reservationId,
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'reservation_id' => $reservationId,
            ],
            'mode' => 'payment',
            'success_url' => $url . '?status=success',
            'cancel_url' => $url . '?status=cancel',
        ]);
    }
    public function checkOrderDiscount(Order $order, int $discountId) {
        if (!$discountId || !$order->discounts()->containg($discountId)) {
            abort(401, 'Order '.$order->id.' does not have #'.$discountId.' discount');
        }
    }
    public function updatePrice(Order $order) {
        $priceWithNoDiscounts = $order->reservation()->price;
        $finalPrice = $priceWithNoDiscounts;

        foreach ($order->discounts() as $discount) {
            $finalPrice *= ((100-$discount->percent)/100);
        }

        $order->update(['price' => $finalPrice]);

        return $order;
    }
}
