<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'number' => (string) $this->faker->unique()->numberBetween(101, 999),
            'room_type_id' => RoomTypeFactory::new(),
            'floor_id' => FloorFactory::new(),
            'rate' => $this->faker->randomFloat(2, 80, 350),
            'status' => Room::STATUS_AVAILABLE,
            'clean_status' => 'clean',
            'is_smoking' => $this->faker->boolean(15),
            'amenities' => $this->faker->randomElements([
                'wifi',
                'tv',
                'minibar',
                'desk',
                'balcony',
            ], $this->faker->numberBetween(2, 5)),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
