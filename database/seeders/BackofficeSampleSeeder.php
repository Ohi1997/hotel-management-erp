<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\WakeUp;
use Illuminate\Database\Seeder;

class BackofficeSampleSeeder extends Seeder
{
    public function run(): void
    {
        $floors = Floor::factory()->count(3)->create();
        $roomTypes = RoomType::factory()->count(4)->create();

        $rooms = collect();
        foreach ($floors as $floor) {
            foreach ($roomTypes as $type) {
                $rooms->push(Room::factory()->create([
                    'floor_id' => $floor->id,
                    'room_type_id' => $type->id,
                ]));
            }
        }

        $customers = Customer::factory()->count(10)->create();

        $bookings = Booking::factory()->count(12)->make()->each(function (Booking $booking) use ($customers, $rooms) {
            $booking->customer_id = $customers->random()->id;
            $booking->room_id = $rooms->random()->id;
            $booking->save();
        });

        foreach ($bookings as $booking) {
            Payment::factory()->count(rand(0, 2))->create([
                'booking_id' => $booking->id,
            ]);
        }

        WakeUp::factory()->count(5)->create();
    }
}
