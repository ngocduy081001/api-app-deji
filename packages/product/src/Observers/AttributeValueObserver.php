<?php

namespace Vendor\Product\Observers;

use Vendor\Product\Models\AttributeValue;
use Vendor\Product\Models\ProductFlat;

class AttributeValueObserver
{
    /**
     * Handle the AttributeValue "updated" event.
     */
    public function updated(AttributeValue $attributeValue): void
    {
        // Refresh flat entries for all variants using this attribute value
        $variantIds = $attributeValue->productVariants()->pluck('product_id')->unique();
        
        foreach ($variantIds as $productId) {
            ProductFlat::refreshForProduct($productId);
        }
    }

    /**
     * Handle the AttributeValue "deleted" event.
     */
    public function deleted(AttributeValue $attributeValue): void
    {
        // Refresh flat entries for all variants using this attribute value
        $variantIds = $attributeValue->productVariants()->pluck('product_id')->unique();
        
        foreach ($variantIds as $productId) {
            ProductFlat::refreshForProduct($productId);
        }
    }
}

