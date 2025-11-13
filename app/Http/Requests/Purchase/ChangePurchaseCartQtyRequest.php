<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class ChangePurchaseCartQtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => __('Product is required'),
            'product_id.exists' => __('Product not found'),
            'quantity.required' => __('Quantity is required'),
            'quantity.integer' => __('Quantity must be a number'),
            'quantity.min' => __('Quantity must be at least 1'),
        ];
    }
}
