<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use App\Models\Room;
use Livewire\Component;

class CheckOut extends Component
{
    public ?int $bookingId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(?int $bookingId = null): void
    {
        $this->form = $this->defaults();
        $this->loadBooking($bookingId);
    }

    public function updatedBookingId(?int $bookingId): void
    {
        $this->loadBooking($bookingId);
    }

    public function complete(): void
    {
        $validated = $this->validate();
        $payload = $validated['form'];

        $booking = Booking::findOrFail($this->bookingId);
        $booking->fill([
            'status' => Booking::STATUS_CHECKED_OUT,
            'actual_check_out_at' => $payload['actual_check_out_at'],
            'notes' => $payload['notes'] ?: $booking->notes,
        ])->save();

        $booking->room?->update(['status' => Room::STATUS_CLEANING]);

        $this->dispatch('booking-checked-out', id: $booking->id);
        $this->dispatch('modal-close', id: 'booking-check-out');
        $this->dispatch('toast', type: 'success', message: 'Booking checked out successfully.');

        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.check-out', [
            'booking' => $this->bookingId ? Booking::with('customer', 'room')->find($this->bookingId) : null,
        ]);
    }

    protected function rules(): array
    {
        return [
            'form.actual_check_out_at' => ['required', 'date'],
            'form.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'actual_check_out_at' => now()->format('Y-m-d\TH:i'),
            'notes' => '',
        ];
    }

    protected function loadBooking(?int $bookingId): void
    {
        $this->bookingId = $bookingId;
        $this->resetValidation();
        $this->form = $this->defaults();

        if ($bookingId) {
            $booking = Booking::findOrFail($bookingId);
            $this->form['actual_check_out_at'] = optional($booking->actual_check_out_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
        }
    }

    protected function resetForm(): void
    {
        $this->bookingId = null;
        $this->form = $this->defaults();
    }
}
