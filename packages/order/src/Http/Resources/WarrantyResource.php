<?php

namespace Vendor\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarrantyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
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
            'warranty_code' => $this->warranty_code,
            'status' => $this->status,
            'active_date' => $this->active_date ? \Carbon\Carbon::parse($this->active_date)->format('d/m/Y') : null,
            'time_expired' => $this->time_expired ? \Carbon\Carbon::parse($this->time_expired)->format('d/m/Y') : null,
            'month' => $this->month,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),
            'qr_path' => $this->qr_path,
            'printed_at' => $this->printed_at?->format('Y-m-d H:i:s'),
            'is_active' => $this->is_active,
            'is_expired' => $this->is_expired,
            'claim_url' => $this->claim_url,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

