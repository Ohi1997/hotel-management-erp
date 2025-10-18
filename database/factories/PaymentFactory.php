<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'paid_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'method' => $this->faker->randomElement(['cash', 'card', 'bank transfer']),
            'reference' => $this->faker->optional()->lexify('PAY????'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
