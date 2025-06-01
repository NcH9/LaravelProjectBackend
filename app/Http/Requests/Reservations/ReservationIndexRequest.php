<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;

class ReservationIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'sort_by' => 'nullable|string|in:id,reservation_start,reservation_end,room_id,user_id',
            'direction' => 'nullable|string|in:asc,desc',
        ];
    }
}
