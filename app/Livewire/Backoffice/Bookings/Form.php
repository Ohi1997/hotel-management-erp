<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?int $bookingId = null;

    /**
     * Backing form state.
     *
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

    public function save(): void
    {
        $validated = $this->validate();
        $payload = $validated['form'];

        $checkIn = CarbonImmutable::parse($payload['check_in_at']);
        $checkOut = CarbonImmutable::parse($payload['check_out_at']);

        $nights = max($checkIn->diffInDays($checkOut), 1);
        $roomRate = Room::findOrFail($payload['room_id'])->rate ?: 0;

        if (! $payload['nightly_rate']) {
            $payload['nightly_rate'] = $roomRate;
        }

        if (! $payload['total_amount']) {
            $payload['total_amount'] = $payload['nightly_rate'] * $nights;
        }

        $booking = Booking::updateOrCreate(
            ['id' => $this->bookingId],
            $payload
        );

        $booking->room?->update([
            'status' => $this->resolveRoomStatus($booking->status),
        ]);

        event(new BookingConfirmed($booking));

        $this->dispatch('booking-saved', id: $booking->id);
        $this->dispatch('modal-close', id: 'booking-form');
        $this->dispatch('toast', type: 'success', message: 'Booking saved successfully.');

        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.form', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'rooms' => Room::with('roomType')
                ->orderBy('number')
                ->get(['id', 'number', 'room_type_id']),
            'statuses' => $this->statusOptions(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'form.customer_id' => ['required', 'exists:customers,id'],
            'form.room_id' => ['required', 'exists:rooms,id'],
            'form.check_in_at' => ['required', 'date'],
            'form.check_out_at' => ['required', 'date', 'after:form.check_in_at'],
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.guest_count' => ['required', 'integer', 'min:1', 'max:8'],
            'form.nightly_rate' => ['nullable', 'numeric', 'min:0'],
            'form.total_amount' => ['nullable', 'numeric', 'min:0'],
            'form.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'customer_id' => null,
            'room_id' => null,
            'check_in_at' => now()->addDay()->format('Y-m-d\TH:i'),
            'check_out_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'status' => Booking::STATUS_RESERVED,
            'guest_count' => 1,
            'nightly_rate' => null,
            'total_amount' => null,
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
            $this->form = array_merge(
                $this->form,
                $booking->only(array_keys($this->form))
            );

            $this->form['check_in_at'] = optional($booking->check_in_at)->format('Y-m-d\TH:i');
            $this->form['check_out_at'] = optional($booking->check_out_at)->format('Y-m-d\TH:i');
        }
    }

    protected function resetForm(): void
    {
        $this->bookingId = null;
        $this->form = $this->defaults();
        $this->resetValidation();
    }

    protected function statusOptions(): array
    {
        return [
            Booking::STATUS_RESERVED => 'Reserved',
            Booking::STATUS_CHECKED_IN => 'Checked In',
            Booking::STATUS_CHECKED_OUT => 'Checked Out',
            Booking::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    protected function resolveRoomStatus(string $status): string
    {
        return match ($status) {
            Booking::STATUS_RESERVED => Room::STATUS_AVAILABLE,
            Booking::STATUS_CHECKED_IN => Room::STATUS_OCCUPIED,
            Booking::STATUS_CHECKED_OUT => Room::STATUS_CLEANING,
            Booking::STATUS_CANCELLED => Room::STATUS_AVAILABLE,
            default => Room::STATUS_AVAILABLE,
        };
    }
}
