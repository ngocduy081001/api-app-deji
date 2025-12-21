<?php

namespace Vendor\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeValueRequest extends FormRequest
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
            'attribute_id' => ['sometimes', 'required', 'integer', 'exists:attributes,id'],
            'value' => ['sometimes', 'required', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
            'color_code' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'string'],
            'price_adjustment' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
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
            'attribute_id.required' => 'ID thuộc tính là bắt buộc',
            'attribute_id.exists' => 'Thuộc tính không tồn tại',
            'value.required' => 'Giá trị thuộc tính là bắt buộc',
            'value.max' => 'Giá trị thuộc tính không được vượt quá 255 ký tự',
            'color_code.regex' => 'Mã màu phải có định dạng hex (VD: #FF0000)',
            'price_adjustment.numeric' => 'Điều chỉnh giá phải là số',
        ];
    }
}

