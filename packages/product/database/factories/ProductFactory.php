<?php

namespace Vendor\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductCategory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $price = $this->faker->numberBetween(50000, 2000000);
        $hasSale = $this->faker->boolean(30);
        $salePrice = $hasSale ? $price * $this->faker->randomFloat(2, 0.6, 0.9) : null;

        return [
            'name' => $this->faker->words(3, true),
            'slug' => null, // Will be auto-generated
            'description' => $this->faker->paragraphs(3, true),
            'short_description' => $this->faker->sentence(15),
            'price' => $price,
            'sale_price' => $salePrice,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'sku' => null, // Will be auto-generated
            'category_id' => ProductCategory::inRandomOrder()->first()?->id,
            'images' => [
                'https://via.placeholder.com/800x600/E74C3C/ffffff?text=Image+1',
                'https://via.placeholder.com/800x600/3498DB/ffffff?text=Image+2',
                'https://via.placeholder.com/800x600/2ECC71/ffffff?text=Image+3',
            ],
            'featured_image' => 'https://via.placeholder.com/800x600/' . $this->faker->hexColor() . '/ffffff?text=Product',
            'is_active' => $this->faker->boolean(85),
            'is_featured' => $this->faker->boolean(20),
            'view_count' => $this->faker->numberBetween(0, 1000),
            'sort_order' => $this->faker->numberBetween(0, 100),
            'meta_data' => [
                'brand' => $this->faker->company(),
                'warranty' => $this->faker->randomElement(['6 tháng', '12 tháng', '24 tháng']),
            ],
        ];
    }
}

