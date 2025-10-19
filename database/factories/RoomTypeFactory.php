<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Standard', 'Deluxe', 'Suite', 'Executive', 'Family']),
            'base_rate' => $this->faker->randomFloat(2, 80, 350),
            'max_occupancy' => $this->faker->numberBetween(1, 6),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
