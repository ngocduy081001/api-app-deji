<?php

namespace Vendor\Product\Observers;

use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductFlat;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        ProductFlat::refreshForProduct($product->id);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        ProductFlat::refreshForProduct($product->id);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        ProductFlat::where('product_id', $product->id)->delete();
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        ProductFlat::refreshForProduct($product->id);
    }
}

