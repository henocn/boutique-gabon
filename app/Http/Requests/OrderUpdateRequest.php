<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_map(fn (OrderStatus $s) => $s->value, OrderStatus::cases()))],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'manager_note' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
