<?php

namespace Vendor\Product\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends BaseProductCategoryRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $this->route();
        $categoryId = $route ? $route->parameter('category') : null;

        $rules = $this->baseRules();

        // Update slug rule to ignore current category
        $rules['slug'][] = Rule::unique('product_categories', 'slug')->ignore($categoryId);

        // Update parent_id rule to prevent self-reference
        $rules['parent_id'][] = Rule::notIn([$categoryId]);

        // Make name optional for update
        $rules['name'][0] = 'sometimes';

        return $rules;
    }
}
