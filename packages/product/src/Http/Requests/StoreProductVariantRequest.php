<?php

namespace Vendor\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'attributes' => ['required', 'array'],
            'attributes.*' => ['required'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'string'],
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
            'product_id.required' => 'ID sản phẩm là bắt buộc',
            'product_id.exists' => 'Sản phẩm không tồn tại',
            'attributes.required' => 'Thuộc tính biến thể là bắt buộc',
            'attributes.array' => 'Thuộc tính phải là mảng',
            'sku.unique' => 'Mã SKU này đã được sử dụng',
            'price.numeric' => 'Giá phải là số',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'sale_price.min' => 'Giá khuyến mãi phải lớn hơn hoặc bằng 0',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên',
            'stock_quantity.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0',
        ];
    }
}

