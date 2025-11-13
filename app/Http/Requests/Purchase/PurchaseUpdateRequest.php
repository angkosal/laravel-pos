<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseUpdateRequest extends FormRequest
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
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => __('Please select a supplier'),
            'supplier_id.exists' => __('Selected supplier does not exist'),
            'purchase_date.required' => __('Purchase date is required'),
            'purchase_date.date' => __('Purchase date must be a valid date'),
            'total_amount.required' => __('Total amount is required'),
            'total_amount.numeric' => __('Total amount must be a number'),
            'total_amount.min' => __('Total amount cannot be negative'),
            'status.required' => __('Status is required'),
            'status.in' => __('Invalid status selected'),
        ];
    }
}
