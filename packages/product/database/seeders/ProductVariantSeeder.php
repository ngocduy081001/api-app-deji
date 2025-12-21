<?php

namespace Vendor\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductVariant;
use Vendor\Product\Models\Attribute;
use Vendor\Product\Models\AttributeValue;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $sizeAttribute = Attribute::where('slug', 'size')->first();
        $colorAttribute = Attribute::where('slug', 'color')->first();

        if (!$sizeAttribute || !$colorAttribute) {
            $this->command->warn('Attributes not found. Please run AttributeSeeder first.');
            return;
        }

        $sizes = AttributeValue::where('attribute_id', $sizeAttribute->id)->get();
        $colors = AttributeValue::where('attribute_id', $colorAttribute->id)->get();

        if ($sizes->isEmpty() || $colors->isEmpty()) {
            $this->command->warn('Attribute values not found.');
            return;
        }

        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        $variantCount = 0;

        foreach ($products as $product) {
            // Chọn random số lượng size và color để tạo variants
            $selectedSizes = $sizes->random(min(3, $sizes->count()));
            $selectedColors = $colors->random(min(4, $colors->count()));

            $sortOrder = 0;

            foreach ($selectedSizes as $size) {
                foreach ($selectedColors as $color) {
                    $stockQuantity = rand(0, 50);
                    
                    // Tạo variant
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => "Size {$size->value} - {$color->value}",
                        'sku' => null, // Auto-generated
                        'attributes' => [
                            'size' => $size->value,
                            'color' => $color->value,
                        ],
                        'price' => null, // Use product price
                        'sale_price' => null,
                        'stock_quantity' => $stockQuantity,
                        'image' => rand(1, 10) > 7 
                            ? "https://via.placeholder.com/800x600/{$this->getColorCode($color->color_code)}/ffffff?text=Variant+{$size->value}+{$color->value}"
                            : null,
                        'is_active' => true,
                        'sort_order' => $sortOrder++,
                    ]);

                    // Attach attribute values
                    $variant->attributeValues()->attach([
                        $size->id,
                        $color->id,
                    ]);

                    $variantCount++;
                }
            }
        }

        $this->command->info("✓ Created {$variantCount} product variants");
    }

    private function getColorCode(?string $colorCode): string
    {
        if (!$colorCode) {
            return 'CCCCCC';
        }
        return ltrim($colorCode, '#');
    }
}

