<?php

namespace Vendor\Product\Observers;

use Vendor\Product\Models\ProductVariant;
use Vendor\Product\Models\ProductFlat;

class ProductVariantObserver
{
    /**
     * Handle the ProductVariant "created" event.
     */
    public function created(ProductVariant $variant): void
    {
        ProductFlat::refreshForProduct($variant->product_id);
    }

    /**
     * Handle the ProductVariant "updated" event.
     */
    public function updated(ProductVariant $variant): void
    {
        ProductFlat::refreshForProduct($variant->product_id);
    }

    /**
     * Handle the ProductVariant "deleted" event.
     */
    public function deleted(ProductVariant $variant): void
    {
        // Delete flat entry for this variant
        ProductFlat::where('variant_id', $variant->id)->delete();
        
        // If this was the last variant, create a flat entry for the product itself
        $remainingVariants = ProductVariant::where('product_id', $variant->product_id)
            ->where('id', '!=', $variant->id)
            ->count();
            
        if ($remainingVariants === 0) {
            ProductFlat::refreshForProduct($variant->product_id);
        }
    }

    /**
     * Handle the ProductVariant "restored" event.
     */
    public function restored(ProductVariant $variant): void
    {
        ProductFlat::refreshForProduct($variant->product_id);
    }
}

