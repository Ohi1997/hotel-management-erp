<?php

namespace Database\Factories;

use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'number' => (string) $this->faker->unique()->numberBetween(100, 999),
            'floor_id' => Floor::factory(),
            'room_type_id' => RoomType::factory(),
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance']),
            'is_clean' => $this->faker->boolean(80),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
