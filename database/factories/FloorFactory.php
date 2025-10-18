<?php

namespace Database\Factories;

use App\Models\Floor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Floor>
 */
class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition(): array
    {
        return [
            'name' => 'Floor '.$this->faker->randomDigitNotZero(),
            'level' => $this->faker->numberBetween(1, 10),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
