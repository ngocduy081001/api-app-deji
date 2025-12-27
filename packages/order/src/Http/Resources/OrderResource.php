<?php

namespace Vendor\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'subtotal' => $this->formatNumericPrice($this->subtotal),
            'tax' => $this->formatNumericPrice($this->tax),
            'shipping_fee' => $this->formatNumericPrice($this->shipping_fee),
            'total' => $this->formatNumericPrice($this->total),
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'appointment_date' => $this->appointment_date?->format('Y-m-d'),
            'appointment_time' => $this->appointment_time?->format('H:i'),
            'appointment_note' => $this->appointment_note,
            'appointment_status' => $this->appointment_status,
            'notes' => $this->notes,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
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

