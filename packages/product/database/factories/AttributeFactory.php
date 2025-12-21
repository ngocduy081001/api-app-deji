<?php

namespace Vendor\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vendor\Product\Models\Attribute;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => null, // Will be auto-generated
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement([
                Attribute::TYPE_SELECT,
                Attribute::TYPE_COLOR,
            ]),
            'is_required' => $this->faker->boolean(30),
            'is_visible' => $this->faker->boolean(90),
            'is_filterable' => $this->faker->boolean(80),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}

