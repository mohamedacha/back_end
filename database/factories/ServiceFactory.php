<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = ["cat1" , "cat2", "cat3" , "cat4"];
        return [
            'service_name' => $this->faker->randomElement($type) ,
            'img' => 'services_imgs/default.png' ,
            'description' => $this->faker->text(),
            'available' => $this->faker->boolean(),
            'created_at' => now(),
        ];
    }
}
