<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class ChangeQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => __('cart.validation.product_id_required'),
            'product_id.exists' => __('cart.validation.product_not_found'),
            'quantity.required' => __('cart.validation.quantity_required'),
            'quantity.min' => __('cart.validation.quantity_min'),
        ];
    }
}
