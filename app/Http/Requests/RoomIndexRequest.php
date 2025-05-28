<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'period' => 'sometimes|array|size:1',
            'period.0.start' => 'nullable|date',
            'period.0.end' => 'nullable|date|after_or_equal:period.0.start',
        ];
    }
}
