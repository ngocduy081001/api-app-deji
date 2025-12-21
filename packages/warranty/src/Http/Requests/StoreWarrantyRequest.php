<?php

namespace Vendor\Warranty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarrantyRequest extends FormRequest
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
            'warranty_code' => ['required', 'string', 'max:255', 'unique:warranties,warranty_code'],
            'status' => ['nullable', 'string', 'in:clear,active,expired'],
            'active_date' => ['nullable', 'date'],
            'time_expired' => ['nullable', 'date', 'after:active_date'],
            'month' => ['nullable', 'integer', 'min:1', 'max:120'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'warranty_code.required' => 'Mã bảo hành là bắt buộc',
            'warranty_code.unique' => 'Mã bảo hành này đã được sử dụng',
            'status.in' => 'Trạng thái không hợp lệ',
            'active_date.date' => 'Ngày kích hoạt không hợp lệ',
            'time_expired.date' => 'Ngày hết hạn không hợp lệ',
            'time_expired.after' => 'Ngày hết hạn phải sau ngày kích hoạt',
            'month.integer' => 'Số tháng phải là số nguyên',
            'month.min' => 'Số tháng phải lớn hơn 0',
            'month.max' => 'Số tháng không được vượt quá 120',
            'customer_id.exists' => 'Khách hàng không tồn tại',
        ];
    }
}
