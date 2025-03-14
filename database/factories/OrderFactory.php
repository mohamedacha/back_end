<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'confirmed' => $this->faker->boolean(),
            'quantity' => $this->faker->numberBetween(1,9000),
            'user_id' => User::pluck('id')->random(),
            'product_id' => Product::inRandomOrder()->value('id'), // Returns an ID or null
            'service_id' => Service::inRandomOrder()->value('id'), // Returns an ID or null
            'created_at' => now() ,
        ];
    }
}
