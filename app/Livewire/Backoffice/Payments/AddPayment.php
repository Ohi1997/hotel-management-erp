<?php

namespace App\Livewire\Backoffice\Payments;

use App\Events\PaymentRecorded;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AddPayment extends Component
{
    public ?int $paymentId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(?int $paymentId = null): void
    {
        $this->form = $this->defaults();
        $this->loadPayment($paymentId);
    }

    public function updatedPaymentId(?int $paymentId): void
    {
        $this->loadPayment($paymentId);
    }

    public function save(): void
    {
        $validated = $this->validate();
        $payload = $validated['form'];

        if (! $payload['customer_id'] && $payload['booking_id']) {
            $payload['customer_id'] = Booking::findOrFail($payload['booking_id'])->customer_id;
        }

        if (! $payload['paid_at']) {
            $payload['paid_at'] = now();
        }

        $payload['recorded_by'] = Auth::id();

        $payment = Payment::updateOrCreate(
            ['id' => $this->paymentId],
            $payload
        );

        event(new PaymentRecorded($payment));

        $this->dispatch('payment-saved', id: $payment->id);
        $this->dispatch('modal-close', id: 'payment-form');
        $this->dispatch('toast', type: 'success', message: 'Payment recorded successfully.');

        $this->resetForm();
    }

    public function updatedFormBookingId(?int $bookingId): void
    {
        if ($bookingId) {
            $booking = Booking::with('customer')->find($bookingId);
            if ($booking) {
                $this->form['customer_id'] = $booking->customer_id;
                if (! $this->form['amount']) {
                    $this->form['amount'] = $booking->total_amount;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.backoffice.payments.add-payment', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'bookings' => Booking::orderByDesc('created_at')->get(['id', 'reference', 'customer_id']),
            'statuses' => $this->statusOptions(),
            'methods' => $this->methods(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'form.booking_id' => ['nullable', 'exists:bookings,id'],
            'form.customer_id' => ['required_without:form.booking_id', 'nullable', 'exists:customers,id'],
            'form.amount' => ['required', 'numeric', 'min:0.01'],
            'form.method' => ['required', Rule::in($this->methods())],
            'form.reference' => ['nullable', 'string', 'max:191'],
            'form.paid_at' => ['nullable', 'date'],
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'booking_id' => null,
            'customer_id' => null,
            'amount' => null,
            'method' => 'cash',
            'reference' => null,
            'paid_at' => now()->format('Y-m-d\TH:i'),
            'status' => Payment::STATUS_COMPLETED,
            'notes' => '',
        ];
    }

    protected function loadPayment(?int $paymentId): void
    {
        $this->paymentId = $paymentId;
        $this->resetValidation();
        $this->form = $this->defaults();

        if ($paymentId) {
            $payment = Payment::findOrFail($paymentId);
            $this->form = array_merge(
                $this->form,
                $payment->only(array_keys($this->form))
            );

            $this->form['paid_at'] = optional($payment->paid_at)->format('Y-m-d\TH:i');
        }
    }

    protected function resetForm(): void
    {
        $this->paymentId = null;
        $this->form = $this->defaults();
        $this->resetValidation();
    }

    protected function statusOptions(): array
    {
        return [
            Payment::STATUS_COMPLETED => 'Completed',
            Payment::STATUS_PENDING => 'Pending',
            Payment::STATUS_FAILED => 'Failed',
        ];
    }

    protected function methods(): array
    {
        return ['cash', 'card', 'transfer', 'mobile', 'other'];
    }
}
