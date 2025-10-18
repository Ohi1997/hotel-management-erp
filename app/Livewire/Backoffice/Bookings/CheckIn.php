<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Livewire\Component;

class CheckIn extends Component
{
    use AuthorizesRequests;

    public ?Booking $booking = null;

    public function mount(int $bookingId): void
    {
        $this->booking = Booking::with(['customer', 'room'])->findOrFail($bookingId);
        $this->authorize('update', $this->booking);
    }

    public function checkIn(): void
    {
        $this->authorize('update', $this->booking);
        $this->booking->update([
            'status' => 'checked_in',
            'check_in_at' => Carbon::now(),
        ]);

        $this->dispatch('booking-checked-in');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.check-in');
    }
}
