<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Bookings\Form as BookingForm;
use App\Livewire\Backoffice\Customers\Form as CustomerForm;
use App\Livewire\Backoffice\Payments\AddPayment;
use App\Livewire\Backoffice\WakeUps\Form as WakeUpForm;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\WakeUp;
use Database\Seeders\RolesAndUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndUsersSeeder::class);
        $this->user = User::role('manager')->first() ?? User::factory()->create();

        if (! $this->user->hasRole('manager')) {
            $this->user->assignRole('manager');
        }

        $this->actingAs($this->user);
    }

    public function test_customer_form_creates_customer(): void
    {
        Livewire::test(CustomerForm::class)
            ->set('form.name', 'Livewire Guest')
            ->set('form.email', 'guest@example.com')
            ->set('form.phone', '+123456789')
            ->call('save');

        $this->assertDatabaseHas('customers', [
            'email' => 'guest@example.com',
        ]);
    }

    public function test_booking_form_creates_booking(): void
    {
        $customer = Customer::factory()->create();
        $roomType = RoomType::factory()->create();
        $floor = Floor::factory()->create();
        $room = Room::factory()->create([
            'room_type_id' => $roomType->id,
            'floor_id' => $floor->id,
        ]);

        $checkIn = now()->addDays(2)->startOfHour();
        $checkOut = $checkIn->copy()->addDays(3);

        Livewire::test(BookingForm::class)
            ->set('form.customer_id', $customer->id)
            ->set('form.room_id', $room->id)
            ->set('form.check_in_at', $checkIn->format('Y-m-d\TH:i'))
            ->set('form.check_out_at', $checkOut->format('Y-m-d\TH:i'))
            ->set('form.guest_count', 2)
            ->call('save');

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'room_id' => $room->id,
        ]);
    }

    public function test_add_payment_component_records_payment(): void
    {
        $booking = Booking::factory()->create();
        $customer = $booking->customer ?? Customer::factory()->create();

        Livewire::test(AddPayment::class)
            ->set('form.booking_id', $booking->id)
            ->set('form.customer_id', $customer->id)
            ->set('form.amount', 120.50)
            ->set('form.method', 'cash')
            ->set('form.status', 'completed')
            ->call('save');

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 120.50,
        ]);
    }

    public function test_wake_up_form_creates_entry(): void
    {
        $booking = Booking::factory()->create();
        $customer = $booking->customer ?? Customer::factory()->create();

        Livewire::test(WakeUpForm::class)
            ->set('form.booking_id', $booking->id)
            ->set('form.customer_id', $customer->id)
            ->set('form.scheduled_for', now()->addDay()->setTime(7, 0)->format('Y-m-d\TH:i'))
            ->set('form.status', 'scheduled')
            ->call('save');

        $this->assertDatabaseHas('wake_ups', [
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
        ]);
    }
}

