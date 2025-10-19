<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use App\Models\Room;
use Livewire\Component;

class CheckIn extends Component
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

    public function confirm(): void
    {
        $validated = $this->validate();
        $payload = $validated['form'];

        $booking = Booking::findOrFail($this->bookingId);
        $booking->fill([
            'status' => Booking::STATUS_CHECKED_IN,
            'actual_check_in_at' => $payload['actual_check_in_at'],
            'notes' => $payload['notes'] ?: $booking->notes,
        ])->save();

        $booking->room?->update(['status' => Room::STATUS_OCCUPIED]);

        $this->dispatch('booking-checked-in', id: $booking->id);
        $this->dispatch('modal-close', id: 'booking-check-in');
        $this->dispatch('toast', type: 'success', message: 'Booking checked in successfully.');

        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.check-in', [
            'booking' => $this->bookingId ? Booking::with('customer', 'room')->find($this->bookingId) : null,
        ]);
    }

    protected function rules(): array
    {
        return [
            'form.actual_check_in_at' => ['required', 'date'],
            'form.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'actual_check_in_at' => now()->format('Y-m-d\TH:i'),
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
            $this->form['actual_check_in_at'] = optional($booking->actual_check_in_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
        }
    }

    protected function resetForm(): void
    {
        $this->bookingId = null;
        $this->form = $this->defaults();
    }
}
