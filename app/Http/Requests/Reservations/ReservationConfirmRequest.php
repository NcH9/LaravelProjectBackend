<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ReservationConfirmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reservation_start' => 'required|date|after_or_equal:today',
            'reservation_end' => 'required|date|after_or_equal:reservation_start',
            'room_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value !== 0 && !DB::table('room')->where('id', $value)->exists()) {
                        $fail('Room does not exist');
                    }
                }
            ]
        ];
    }
}
