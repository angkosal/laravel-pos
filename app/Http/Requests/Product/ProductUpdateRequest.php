<?php

namespace App\Http\Requests\Product;

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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB max
            'barcode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')->ignore($this->product)
            ],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('product.validation.name_required'),
            'barcode.required' => __('product.validation.barcode_required'),
            'barcode.unique' => __('product.validation.barcode_unique'),
            'price.required' => __('product.validation.price_required'),
            'price.decimal' => __('product.validation.price_decimal'),
            'quantity.required' => __('product.validation.quantity_required'),
            'quantity.min' => __('product.validation.quantity_min'),
            'image.max' => __('product.validation.image_max'),
        ];
    }
}
