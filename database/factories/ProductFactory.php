<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends Factory<Product>
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
        'name' => fake()->words(3, true),
        'description' => fake()->sentence(),
        'price' => fake()->randomFloat(2, 10000, 500000),
        'image' => null,
        'category_id' => Category::factory(),
        ];
    }
}
