<?php

namespace App\Services;

use App\Models\Discount;

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
    public function create($data)
    {
        Discount::create($data);
    }
    public function update($id, $data)
    {
        $discount = Discount::find($id);
        $discount->update($data);
    }
    public function delete($id)
    {
        Discount::destroy($id);
    }
}
