<?php

namespace Vendor\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Bạn có thể thêm logic authorize ở đây
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Lấy ID từ route parameter (có thể là model object hoặc ID)
        $product = $this->route('product');
        $productId = is_object($product) ? $product->id : $product;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $productId],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'], // Legacy support
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:product_categories,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string'],
            'featured_image' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'view_count' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta_data' => ['nullable', 'array'],
            'specifications' => ['nullable', 'array'],
            'specifications.*.name' => ['required_with:specifications', 'string', 'max:255'],
            'specifications.*.value' => ['required_with:specifications', 'string', 'max:500'],
            'replacement_price' => ['nullable', 'numeric', 'min:0'],
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
            'name.required' => 'Tên sản phẩm là bắt buộc',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug này đã được sử dụng',
            'price.required' => 'Giá sản phẩm là bắt buộc',
            'price.numeric' => 'Giá sản phẩm phải là số',
            'price.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'sale_price.min' => 'Giá khuyến mãi phải lớn hơn hoặc bằng 0',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên',
            'stock_quantity.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0',
            'sku.unique' => 'Mã SKU này đã được sử dụng',
            'category_id.exists' => 'Danh mục không tồn tại',
            'categories.array' => 'Danh mục phải là mảng',
            'categories.*.exists' => 'Một hoặc nhiều danh mục không tồn tại',
            'images.array' => 'Ảnh phải là mảng',
        ];
    }
}
