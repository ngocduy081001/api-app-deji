<?php

namespace Vendor\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the base validation rules.
     */
    protected function baseRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục là bắt buộc',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug này đã tồn tại',
            'slug.max' => 'Slug không được vượt quá 255 ký tự',
            'parent_id.exists' => 'Danh mục cha không tồn tại',
            'parent_id.not_in' => 'Danh mục không thể là cha của chính nó',
            'is_active.boolean' => 'Trạng thái phải là true hoặc false',
            'is_featured.boolean' => 'Trạng thái nổi bật phải là true hoặc false',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên',
            'sort_order.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0',
        ];
    }
}
