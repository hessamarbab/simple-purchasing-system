<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word() .' ' . fake()->company(),
            'price' => fake()->numberBetween(1000000, 10000000),
            'inventory' => fake()->numberBetween(1000, 10000)
        ];
    }
}
