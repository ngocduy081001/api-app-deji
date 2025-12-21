<?php

namespace Vendor\Warranty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateWarrantyQrRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:200'],
            'month' => ['nullable', 'integer', 'min:1', 'max:120'],
            'code_prefix' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Vui lòng nhập số lượng QR cần tạo.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
            'quantity.max' => 'Bạn chỉ có thể tạo tối đa 200 QR mỗi lần.',
            'month.min' => 'Thời hạn bảo hành tối thiểu là 1 tháng.',
            'month.max' => 'Thời hạn bảo hành tối đa là 120 tháng.',
        ];
    }
}
