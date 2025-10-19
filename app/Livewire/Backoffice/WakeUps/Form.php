<?php

namespace App\Livewire\Backoffice\WakeUps;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\WakeUp;
use Livewire\Component;

class Form extends Component
{
    public ?int $wakeUpId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(?int $wakeUpId = null): void
    {
        $this->form = $this->defaults();
        $this->loadWakeUp($wakeUpId);
    }

    public function updatedWakeUpId(?int $wakeUpId): void
    {
        $this->loadWakeUp($wakeUpId);
    }

    public function updatedFormBookingId(?int $bookingId): void
    {
        if ($bookingId) {
            $booking = Booking::with('customer')->find($bookingId);
            if ($booking) {
                $this->form['customer_id'] = $booking->customer_id;
            }
        }
    }

    public function save(): void
    {
        $data = $this->validate();
        $payload = $data['form'];

        if (! $payload['customer_id'] && $payload['booking_id']) {
            $payload['customer_id'] = Booking::findOrFail($payload['booking_id'])->customer_id;
        }

        $wakeUp = WakeUp::updateOrCreate(
            ['id' => $this->wakeUpId],
            $payload
        );

        $this->dispatch('wake-up-saved', id: $wakeUp->id);
        $this->dispatch('modal-close', id: 'wake-up-form');
        $this->dispatch('toast', type: 'success', message: 'Wake-up saved.');

        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.backoffice.wake-ups.form', [
            'bookings' => Booking::orderByDesc('created_at')->get(['id', 'reference', 'customer_id']),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'statuses' => $this->statusOptions(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'form.booking_id' => ['nullable', 'exists:bookings,id'],
            'form.customer_id' => ['required_without:form.booking_id', 'nullable', 'exists:customers,id'],
            'form.scheduled_for' => ['required', 'date'],
            'form.completed_at' => ['nullable', 'date', 'after_or_equal:form.scheduled_for'],
            'form.status' => ['required', 'in:scheduled,completed,cancelled'],
            'form.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'booking_id' => null,
            'customer_id' => null,
            'scheduled_for' => now()->addDay()->format('Y-m-d\TH:i'),
            'completed_at' => null,
            'status' => WakeUp::STATUS_SCHEDULED,
            'notes' => '',
        ];
    }

    protected function loadWakeUp(?int $wakeUpId): void
    {
        $this->wakeUpId = $wakeUpId;
        $this->resetValidation();
        $this->form = $this->defaults();

        if ($wakeUpId) {
            $wakeUp = WakeUp::findOrFail($wakeUpId);
            $this->form = array_merge(
                $this->form,
                $wakeUp->only(array_keys($this->form))
            );

            $this->form['scheduled_for'] = optional($wakeUp->scheduled_for)->format('Y-m-d\TH:i');
            $this->form['completed_at'] = optional($wakeUp->completed_at)?->format('Y-m-d\TH:i');
        }
    }

    protected function resetForm(): void
    {
        $this->wakeUpId = null;
        $this->form = $this->defaults();
        $this->resetValidation();
    }

    protected function statusOptions(): array
    {
        return [
            WakeUp::STATUS_SCHEDULED => 'Scheduled',
            WakeUp::STATUS_COMPLETED => 'Completed',
            WakeUp::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
