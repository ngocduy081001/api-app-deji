<?php

namespace Vendor\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'slug' => $this->product->slug,
                    'sku' => $this->product->sku,
                    'featured_image' => $this->product->featured_image,
                ];
            }),
            'quantity' => $this->quantity,
            'price' => $this->formatNumericPrice($this->price),
            'total' => $this->formatNumericPrice($this->total),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
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

        // Otherwise return as float
        return $price;
    }
}

