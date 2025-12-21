<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $images = $this->images ?? [];
        $foreign_images = [];
        foreach ($images as $index => $image) {
            if (!empty($image)) {
                $foreign_images[] = [
                    'id' => $index + 1,
                    'image' => $image,
                    'product_id' => $this->id,
                ];
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'shortdescription' => $this->short_description,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'specifications' => json_decode(($this->meta_data['specifications'] ?? null) ?: '{}', true) ?? [],
            'foreign_images' => $foreign_images,
            'featured_image' => $this->featured_image,
            'type' => $this->variant_id ? 'variant' : 'simple',
            'gallery_images' => $this->images,
            'categories' => $this->categories,
            'image' => $this->featured_image,
        ];
    }
}
