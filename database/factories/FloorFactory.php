<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Floor>
 */
class FloorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Level ' . $this->faker->numberBetween(1, 10),
            'level' => $this->faker->numberBetween(1, 15),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
