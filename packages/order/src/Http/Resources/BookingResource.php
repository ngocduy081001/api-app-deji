<?php

namespace Vendor\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),
            'showroom_id' => $this->showroom_id,
            'showroom' => $this->whenLoaded('showroom', function () {
                return [
                    'id' => $this->showroom->id,
                    'name' => $this->showroom->name,
                    'address' => $this->showroom->address,
                    'phone' => $this->showroom->phone,
                    'email' => $this->showroom->email,
                ];
            }),
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
            'price' => $this->formatNumericPrice($this->price),
            'date' => $this->date?->format('Y-m-d'),
            'time' => $this->time,
            'status' => $this->status,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
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

