<?php

namespace App\Livewire\Backoffice\WakeUps;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\WakeUp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

    public ?WakeUp $wakeUp = null;

    public ?int $booking_id = null;
    public ?int $customer_id = null;
    public string $scheduled_for = '';
    public ?string $status = 'scheduled';
    public ?string $notes = null;

    public function mount(?int $wakeUpId = null): void
    {
        $this->scheduled_for = now()->format('Y-m-d\TH:i');
        if ($wakeUpId) {
            $this->loadWakeUp($wakeUpId);
        } else {
            $this->authorize('create', WakeUp::class);
        }
    }

    #[On('wake-up-edit')]
    public function loadFromEvent(array $payload): void
    {
        $this->loadWakeUp($payload['id']);
    }

    protected function loadWakeUp(int $id): void
    {
        $this->wakeUp = WakeUp::findOrFail($id);
        $this->authorize('update', $this->wakeUp);
        $this->fill($this->wakeUp->only(['booking_id', 'customer_id', 'status', 'notes']));
        $this->scheduled_for = optional($this->wakeUp->scheduled_for)->format('Y-m-d\TH:i');
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'scheduled_for' => ['required', 'date'],
            'status' => ['required', 'in:scheduled,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $this->authorize($this->wakeUp ? 'update' : 'create', $this->wakeUp ? $this->wakeUp : WakeUp::class);

        $wakeUp = WakeUp::updateOrCreate(
            ['id' => $this->wakeUp?->id],
            [
                'booking_id' => $data['booking_id'],
                'customer_id' => $data['customer_id'],
                'scheduled_for' => $data['scheduled_for'],
                'status' => $data['status'],
                'notes' => $data['notes'],
            ]
        );

        if ($wakeUp->status === 'completed' && ! $wakeUp->completed_at) {
            $wakeUp->update(['completed_at' => now()]);
        }

        $this->dispatch('wake-up-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $customers = Customer::orderBy('name')->get();
        $bookings = Booking::with('customer')->orderByDesc('check_in_at')->limit(30)->get();

        if ($this->booking_id && $bookings->where('id', $this->booking_id)->isEmpty()) {
            if ($selected = Booking::with('customer')->find($this->booking_id)) {
                $bookings->prepend($selected);
            }
        }

        return view('livewire.backoffice.wake-ups.form', [
            'customers' => $customers,
            'bookings' => $bookings,
        ]);
    }
}
