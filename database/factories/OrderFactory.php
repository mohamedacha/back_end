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
            'confirmed' => $this->faker()->boolean(),
            'quantity' => $this->faker()->numberBetween(1,9000),
            'user_id' => User::pluck('id')->random(),
            'product_id' => optional(Product::pluck('id')->random()),
            'service_id' => optional(Service::pluck('id')->random()),
            'created_at' => now() ,
        ];
    }
}
