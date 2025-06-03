<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ReservationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'reservation_start' => 'required|date|after_or_equal:today',
            'reservation_end' => 'required|date|after_or_equal:reservation_start',
            'room_number' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value == 0) {
                        return;
                    }

                    if (!DB::table('rooms')->where('number', $value)->exists()) {
                        $fail('This room does not exist');
                    }
                },
            ],
        ];
    }
}
