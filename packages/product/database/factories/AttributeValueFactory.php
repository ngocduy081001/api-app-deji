<?php

namespace Vendor\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vendor\Product\Models\AttributeValue;
use Vendor\Product\Models\Attribute;

class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

    public function definition(): array
    {
        return [
            'attribute_id' => Attribute::inRandomOrder()->first()?->id ?? Attribute::factory(),
            'value' => $this->faker->word(),
            'label' => $this->faker->word(),
            'color_code' => $this->faker->boolean(50) ? $this->faker->hexColor() : null,
            'image' => null,
            'price_adjustment' => $this->faker->randomElement([0, 0, 0, 10000, 20000, -10000]),
            'is_active' => $this->faker->boolean(90),
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }
}

