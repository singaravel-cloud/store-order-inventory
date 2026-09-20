<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'code' => strtoupper(fake()->unique()->bothify('PROD-####')),
            'price' => fake()->randomFloat(2, 50, 5000),
            'tax_percentage' => fake()->randomElement([0, 5, 12, 18, 28]),
            'stock_on_hand' => fake()->numberBetween(0, 100),
        ];
    }
}