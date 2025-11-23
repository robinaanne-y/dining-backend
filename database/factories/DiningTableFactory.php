<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiningTable>
 */
class DiningTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => \App\Models\Restaurant::factory(),
            'table_number' => $this->faker->unique()->numberBetween(1, 100),
            'seating_capacity' => $this->faker->numberBetween(2, 10),
            'status' => $this->faker->randomElement(['available', 'occupied', 'reserved']),
            'qr_token' => $this->faker->uuid(),
        ];
    }
}
