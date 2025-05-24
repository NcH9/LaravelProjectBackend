<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount_id' => 'sometimes|exists:discounts,id',
            'name' => 'required|string',
            'percent' => 'required|numeric'
        ];
    }
}
