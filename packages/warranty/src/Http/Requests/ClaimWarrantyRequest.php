<?php

namespace Vendor\Warranty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimWarrantyRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập họ tên.',
            'customer_email.email' => 'Email không hợp lệ.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
        ];
    }
}
