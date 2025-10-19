<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\WakeUp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndUsersSeeder::class,
        ]);

        $customers = Customer::factory(20)->create();
        $floors = Floor::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Ground Floor', 'level' => 0],
                ['name' => 'Level 1', 'level' => 1],
                ['name' => 'Level 2', 'level' => 2],
                ['name' => 'Level 3', 'level' => 3],
                ['name' => 'Penthouse', 'level' => 10],
            )
            ->create();

        $roomTypes = RoomType::factory()
            ->count(4)
            ->sequence(
                ['name' => 'Standard Room', 'base_rate' => 120],
                ['name' => 'Deluxe Room', 'base_rate' => 180],
                ['name' => 'Executive Suite', 'base_rate' => 260],
                ['name' => 'Family Suite', 'base_rate' => 220],
            )
            ->create();

        $rooms = Room::factory()
            ->count(24)
            ->make()
            ->each(function (Room $room) use ($floors, $roomTypes): void {
                $room->floor_id = $floors->random()->id;
                $room->room_type_id = $roomTypes->random()->id;
                $room->save();
            });

        $bookings = Collection::times(18, function () use ($customers, $rooms) {
            /** @var \App\Models\Room $room */
            $room = $rooms->random();

            /** @var \App\Models\Booking $booking */
            $booking = Booking::factory()->make([
                'customer_id' => $customers->random()->id,
                'room_id' => $room->id,
            ]);

            $nights = max($booking->check_in_at->diffInDays($booking->check_out_at), 1);
            $booking->nightly_rate = $room->rate;
            $booking->total_amount = $room->rate * $nights;
            $booking->save();

            return $booking;
        });

        $bookings->each(function (Booking $booking): void {
            if ($booking->status === Booking::STATUS_CHECKED_IN) {
                $booking->room->update(['status' => Room::STATUS_OCCUPIED]);
            }

            Payment::factory()
                ->count(rand(0, 2))
                ->state(function () use ($booking) {
                    return [
                        'booking_id' => $booking->id,
                        'customer_id' => $booking->customer_id,
                        'amount' => max(50, $booking->total_amount / max(1, rand(1, 3))),
                        'status' => Payment::STATUS_COMPLETED,
                    ];
                })
                ->create();
        });

        WakeUp::factory()
            ->count(10)
            ->make()
            ->each(function (WakeUp $wakeUp) use ($bookings): void {
                $booking = $bookings->random();
                $wakeUp->booking_id = $booking->id;
                $wakeUp->customer_id = $booking->customer_id;
                $wakeUp->scheduled_for = $booking->check_in_at->copy()->addDays(rand(0, 2))->setTime(rand(5, 9), 0);
                $wakeUp->save();
            });
    }
}
