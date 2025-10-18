<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CheckOut extends Component
{
    use AuthorizesRequests;

    public ?Booking $booking = null;
    public string $notes = '';

    public function mount(int $bookingId): void
    {
        $this->booking = Booking::with(['customer', 'payments'])->findOrFail($bookingId);
        $this->authorize('update', $this->booking);
    }

    public function checkOut(): void
    {
        $this->authorize('update', $this->booking);
        $paid = $this->booking->payments()->sum('amount');
        $balance = max($this->booking->total_amount - $paid, 0);

        $this->booking->update([
            'status' => 'checked_out',
            'check_out_at' => now(),
            'balance_due' => $balance,
            'notes' => $this->notes ?: $this->booking->notes,
        ]);

        $this->dispatch('booking-checked-out');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.check-out', [
            'paymentsTotal' => $this->booking?->payments()->sum('amount') ?? 0,
        ]);
    }
}
