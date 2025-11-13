<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class AddToPurchaseCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barcode' => 'required|string|exists:products,barcode',
        ];
    }

    public function messages(): array
    {
        return [
            'barcode.required' => __('Barcode is required'),
            'barcode.exists' => __('Product not found with this barcode'),
        ];
    }
}
