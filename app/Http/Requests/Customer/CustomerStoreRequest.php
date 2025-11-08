<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => __('customer.validation.first_name_required'),
            'first_name.max' => __('customer.validation.first_name_max'),
            'last_name.required' => __('customer.validation.last_name_required'),
            'last_name.max' => __('customer.validation.last_name_max'),
            'email.email' => __('customer.validation.email_invalid'),
            'phone.max' => __('customer.validation.phone_max'),
            'avatar.image' => __('customer.validation.avatar_image'),
            'avatar.max' => __('customer.validation.avatar_max'),
        ];
    }
}
