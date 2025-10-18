<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomType>
 */
class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->unique()->word).' Suite',
            'capacity' => $this->faker->numberBetween(1, 4),
            'base_rate' => $this->faker->randomFloat(2, 50, 500),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
