<?php

namespace Vendor\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vendor\Product\Models\ProductCategory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => null, // Will be auto-generated
            'description' => $this->faker->sentence(10),
            'parent_id' => null,
            'image' => 'https://via.placeholder.com/400x300/4A90E2/ffffff?text=Category',
            'is_active' => $this->faker->boolean(90),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}

