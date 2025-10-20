<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Hotel',
            'code' => strtoupper($this->faker->unique()->lexify('HTL???')),
            'tax_id' => $this->faker->optional()->numerify('TAX#####'),
            'timezone' => 'UTC',
            'currency' => 'USD',
            'language' => 'en',
            'address_line1' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->countryCode(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
            'default_check_in' => '15:00',
            'default_check_out' => '11:00',
            'default_max_guests' => 2,
            'default_deposit' => 0,
            'cancellation_policy' => '24-hour cancellation required.',
            'is_primary' => true,
            'is_active' => true,
        ];
    }
}
