<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'category' => $this->faker->randomElement(['Appetizers', 'Main Course', 'Desserts', 'Beverages']),
            'status' => $this->faker->randomElement(['available', 'unavailable', 'inactive']),
            'restaurant_id' => \App\Models\Restaurant::factory(),
        ];
    }
}
