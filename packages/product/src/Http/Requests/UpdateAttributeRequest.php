<?php

namespace Vendor\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\Product\Models\Attribute;

class UpdateAttributeRequest extends FormRequest
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
        $attributeId = $this->route('attribute');
        
        $validTypes = implode(',', [
            Attribute::TYPE_SELECT,
            Attribute::TYPE_COLOR,
            Attribute::TYPE_TEXT,
            Attribute::TYPE_NUMBER,
        ]);

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:attributes,slug,' . $attributeId],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'required', 'string', 'in:' . $validTypes],
            'is_required' => ['nullable', 'boolean'],
            'is_visible' => ['nullable', 'boolean'],
            'is_filterable' => ['nullable', 'boolean'],
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
            'name.required' => 'Tên thuộc tính là bắt buộc',
            'name.max' => 'Tên thuộc tính không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug này đã được sử dụng',
            'type.required' => 'Loại thuộc tính là bắt buộc',
            'type.in' => 'Loại thuộc tính không hợp lệ',
        ];
    }
}

