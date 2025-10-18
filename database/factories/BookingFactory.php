<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('-1 week', '+1 week');
        $checkOut = (clone $checkIn)->modify('+'.rand(1, 5).' days');

        return [
            'reference' => 'BKG-'.$this->faker->unique()->numerify('######'),
            'customer_id' => Customer::factory(),
            'room_id' => Room::factory(),
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'checked_in', 'checked_out']),
            'total_amount' => $this->faker->randomFloat(2, 100, 1000),
            'balance_due' => $this->faker->randomFloat(2, 0, 500),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
