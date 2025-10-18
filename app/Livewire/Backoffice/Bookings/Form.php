<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

    public ?Booking $booking = null;

    public ?int $customer_id = null;
    public ?int $room_id = null;
    public string $check_in_at = '';
    public ?string $check_out_at = null;
    public string $status = 'pending';
    public string $total_amount = '0';
    public string $balance_due = '0';
    public ?string $notes = null;

    public function mount(?int $bookingId = null): void
    {
        $this->check_in_at = now()->format('Y-m-d\TH:i');
        if ($bookingId) {
            $booking = Booking::findOrFail($bookingId);
            $this->authorize('update', $booking);
            $this->loadBookingModel($booking);
        } else {
            $this->authorize('create', Booking::class);
        }
    }

    protected function loadBookingModel(Booking $booking): void
    {
        $this->booking = $booking;
        $this->customer_id = $booking->customer_id;
        $this->room_id = $booking->room_id;
        $this->check_in_at = optional($booking->check_in_at)->format('Y-m-d\TH:i');
        $this->check_out_at = optional($booking->check_out_at)?->format('Y-m-d\TH:i');
        $this->status = $booking->status;
        $this->total_amount = (string) $booking->total_amount;
        $this->balance_due = (string) $booking->balance_due;
        $this->notes = $booking->notes;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', ValidationRule::exists('customers', 'id')],
            'room_id' => ['required', ValidationRule::exists('rooms', 'id')],
            'check_in_at' => ['required', 'date'],
            'check_out_at' => ['nullable', 'date', 'after_or_equal:check_in_at'],
            'status' => ['required', ValidationRule::in(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'balance_due' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $payload = [
            'customer_id' => $data['customer_id'],
            'room_id' => $data['room_id'],
            'check_in_at' => Carbon::parse($data['check_in_at']),
            'check_out_at' => $data['check_out_at'] ? Carbon::parse($data['check_out_at']) : null,
            'status' => $data['status'],
            'total_amount' => $data['total_amount'],
            'balance_due' => $data['balance_due'],
            'notes' => $data['notes'],
            'updated_by' => Auth::id(),
        ];

        if ($this->booking) {
            $this->authorize('update', $this->booking);
            $this->booking->update($payload);
            $booking = $this->booking->refresh();
        } else {
            $this->authorize('create', Booking::class);
            $payload['reference'] = $this->generateReference();
            $payload['created_by'] = Auth::id();
            $booking = Booking::create($payload);
            $this->booking = $booking;
        }

        $this->dispatch('booking-saved', id: $booking->id);
        $this->dispatch('close-modal');
    }

    protected function generateReference(): string
    {
        $seed = now()->format('YmdHis');
        return 'BKG-'.$seed.'-'.strtoupper(substr(uniqid('', true), -4));
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.form', [
            'customers' => Customer::orderBy('name')->get(),
            'rooms' => Room::with(['floor', 'roomType'])->orderBy('number')->get(),
        ]);
    }
}
