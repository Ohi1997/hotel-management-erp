<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id' => BookingFactory::new(),
            'customer_id' => function (array $attributes) {
                $booking = Booking::find($attributes['booking_id']);

                return $booking?->customer_id;
            },
            'amount' => $this->faker->randomFloat(2, 50, 800),
            'method' => $this->faker->randomElement(['cash', 'card', 'transfer', 'mobile']),
            'reference' => $this->faker->optional()->lexify('PMT-??????'),
            'paid_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
            'status' => $this->faker->randomElement([
                Payment::STATUS_COMPLETED,
                Payment::STATUS_PENDING,
            ]),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
