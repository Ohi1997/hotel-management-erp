<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\WakeUp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WakeUp>
 */
class WakeUpFactory extends Factory
{
    protected $model = WakeUp::class;

    public function definition(): array
    {
        $scheduled = $this->faker->dateTimeBetween('now', '+3 days');

        return [
            'booking_id' => Booking::factory(),
            'customer_id' => Customer::factory(),
            'scheduled_for' => $scheduled,
            'completed_at' => null,
            'status' => 'scheduled',
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
