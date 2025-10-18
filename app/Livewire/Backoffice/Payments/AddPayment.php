<?php

namespace App\Livewire\Backoffice\Payments;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddPayment extends Component
{
    use AuthorizesRequests;

    public ?Booking $booking = null;

    public ?int $booking_id = null;
    public string $amount = '0';
    public string $paid_at = '';
    public ?string $method = null;
    public ?string $reference = null;
    public ?string $notes = null;

    public function mount(?int $bookingId = null): void
    {
        $this->authorize('create', Payment::class);
        $this->paid_at = now()->format('Y-m-d\TH:i');
        if ($bookingId) {
            $this->loadBooking($bookingId);
        }
    }

    public function updatedBookingId($value): void
    {
        if ($value) {
            $this->loadBooking((int) $value);
        }
    }

    protected function loadBooking(int $bookingId): void
    {
        $this->booking = Booking::with(['customer', 'payments'])->findOrFail($bookingId);
        $this->authorize('view', $this->booking);
        $this->booking_id = $this->booking->id;
        $balance = max($this->booking->total_amount - $this->booking->payments()->sum('amount'), 0);
        $this->amount = $balance > 0 ? (string) $balance : $this->amount;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', Payment::class);
        $data = $this->validate();

        $payment = Payment::create([
            'booking_id' => $data['booking_id'],
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'],
            'method' => $data['method'],
            'reference' => $data['reference'],
            'notes' => $data['notes'],
            'received_by' => Auth::id(),
        ]);

        $booking = $payment->booking;
        $paid = $booking->payments()->sum('amount');
        $booking->update([
            'balance_due' => max($booking->total_amount - $paid, 0),
        ]);

        $this->dispatch('payment-recorded');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $bookings = Booking::with('customer')->orderByDesc('check_in_at')->limit(30)->get();

        if ($this->booking_id && $bookings->where('id', $this->booking_id)->isEmpty()) {
            if ($selected = Booking::with('customer')->find($this->booking_id)) {
                $bookings->prepend($selected);
            }
        }

        return view('livewire.backoffice.payments.add-payment', [
            'bookings' => $bookings,
        ]);
    }
}
