<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\WakeUp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WakeUp>
 */
class WakeUpFactory extends Factory
{
    protected $model = WakeUp::class;

    public function definition(): array
    {
        $scheduled = $this->faker->dateTimeBetween('+1 day', '+2 weeks');

        return [
            'booking_id' => BookingFactory::new(),
            'customer_id' => function (array $attributes) {
                $booking = Booking::find($attributes['booking_id']);

                return $booking?->customer_id;
            },
            'scheduled_for' => $scheduled,
            'completed_at' => null,
            'status' => WakeUp::STATUS_SCHEDULED,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
