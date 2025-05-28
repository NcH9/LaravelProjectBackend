<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function create(int $reservationId, array $data) {
        Order::insert([
            'user_id' => $data['user_id'],
            'reservation_id' => $reservationId,
            'price' => $data['price'],
            'is_paid' => false,
        ]);
    }
    public function update(array $data) {
        $isPaid = !empty($data['is_paid']) ? $data['is_paid'] : false;


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
