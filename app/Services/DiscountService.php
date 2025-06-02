<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Order;
use App\Models\User;

class DiscountService
{
    public function getAll()
    {
        return Discount::all()->toArray();
    }
    public function getById($id)
    {
        return Discount::find($id);
    }
    public function getUserDiscounts(?User $user)
    {
        return $user->discounts();
    }
    public function create($data)
    {
        Discount::create($data);
    }
    public function update(Discount $discount, array $data)
    {
        $discount->update($data);
    }
    public function delete($id)
    {
        Discount::destroy($id);
    }
}
