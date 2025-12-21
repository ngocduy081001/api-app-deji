<?php

namespace Vendor\Product\Http\Requests;

use Illuminate\Validation\Rule;

class StoreProductCategoryRequest extends BaseProductCategoryRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = $this->baseRules();
        $rules['slug'][] = 'unique:product_categories,slug';

        return $rules;
    }
}
