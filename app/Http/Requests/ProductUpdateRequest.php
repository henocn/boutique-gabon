<?php

namespace App\Http\Requests;

use App\Enums\Country;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'description_html' => ['nullable', 'string'],
            'price_buy' => ['required', 'integer', 'min:0'],
            'price_sell' => ['required', 'integer', 'min:0'],
            'price_shipping' => ['required', 'integer', 'min:0'],
            'manager_id' => ['required', 'exists:users,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(array_map(fn (ProductStatus $s) => $s->value, ProductStatus::cases()))],
            'countries' => ['required', 'array', 'min:1'],
            'countries.*' => ['string', Rule::in(array_map(fn (Country $c) => $c->value, Country::cases()))],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif,jfif', 'max:2048'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:product_images,id'],
        ];
    }
}
