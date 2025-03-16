<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
        $category = ["cat1" , "cat2", "cat3" , "cat4"];
        return [
            'product_name' => $this->faker->word(), // Use `word()` for a product name
            'img' => 'default.png', // Fixed typo in file extension
            'price' => $this->faker->numberBetween(1, 1000), // Corrected `random_int()`
            'category' => $this->faker->randomElement($category), // Selects a random category
            'description' => $this->faker->text(),
            'quantity' => $this->faker->numberBetween(1, 1000), // Corrected `random_int()`
            'created_at' => now(),
        ];
    }
}
