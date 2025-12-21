<?php

namespace Vendor\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vendor\Product\Models\ProductVariant;
use Vendor\Product\Models\Product;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $price = $this->faker->boolean(30) ? $this->faker->numberBetween(50000, 2000000) : null;
        $salePrice = ($price && $this->faker->boolean(30)) ? $price * 0.8 : null;

        return [
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'name' => null, // Will be auto-generated from attributes
            'sku' => null, // Will be auto-generated
            'attributes' => [], // Will be set by seeder
            'price' => $price,
            'sale_price' => $salePrice,
            'stock_quantity' => $this->faker->numberBetween(0, 50),
            'image' => $this->faker->boolean(40) 
                ? 'https://via.placeholder.com/800x600/' . $this->faker->hexColor() . '/ffffff?text=Variant'
                : null,
            'is_active' => $this->faker->boolean(85),
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }
}

