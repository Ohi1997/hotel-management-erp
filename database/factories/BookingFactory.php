<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('+1 day', '+1 month');
        $checkOut = (clone $checkIn)->modify('+' . $this->faker->numberBetween(1, 7) . ' days');
        $rate = $this->faker->randomFloat(2, 80, 350);
        $nights = max($checkIn->diff($checkOut)->days, 1);

        return [
            'reference' => 'BK-' . $this->faker->unique()->numerify('######'),
            'customer_id' => CustomerFactory::new(),
            'room_id' => RoomFactory::new(),
            'status' => $this->faker->randomElement([
                Booking::STATUS_RESERVED,
                Booking::STATUS_CHECKED_IN,
                Booking::STATUS_CHECKED_OUT,
            ]),
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'guest_count' => $this->faker->numberBetween(1, 4),
            'nightly_rate' => $rate,
            'total_amount' => $rate * $nights,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
