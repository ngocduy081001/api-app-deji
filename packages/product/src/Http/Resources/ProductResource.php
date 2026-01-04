<?php

namespace Vendor\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->formatNumericPrice($this->price),
            'sale_price' => $this->formatNumericPrice($this->sale_price),
            'price_formatted' => $this->formatPrice($this->price),
            'sale_price_formatted' => $this->formatPrice($this->sale_price),
            'price_off' => $this->formatNumericPrice($this->price_off),
            'price_off_formatted' => $this->formatPrice($this->price_off),
            'stock_quantity' => $this->stock_quantity,
            'sku' => $this->sku,
            'categories' => $this->categories,
            'images' => $this->images,
            'thumbnail' => $this->featured_image,
            'specifications' => json_decode(($this->meta_data['specifications'] ?? null) ?: '{}', true) ?? [],

        ];
    }

    /**
     * Format numeric price without trailing .00 (returns number)
     */
    private function formatNumericPrice($price)
    {
        if ($price === null || $price === '') {
            return null;
        }

        $price = (float) $price;

        // If it's a whole number, return as integer
        if ($price == (int) $price) {
            return (int) $price;
        }

        // Otherwise return as float (will automatically remove trailing zeros when converted to JSON)
        return $price;
    }

    /**
     * Format price without trailing .00 (returns formatted string)
     */
    private function formatPrice($price): string
    {
        if ($price === null || $price === '') {
            return '';
        }

        // Convert to float and check if it's a whole number
        $price = (float) $price;

        // If it's a whole number, format without decimals
        if ($price == (int) $price) {
            return number_format($price, 0, ',', '.');
        }

        // Otherwise format with decimals but remove trailing zeros
        return rtrim(rtrim(number_format($price, 2, ',', '.'), '0'), ',');
    }
}
