<?php

namespace App\Livewire\Backoffice\Payments;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.backoffice')]
class Index extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Payment::class);
    }

    #[Url(history: true)]
    public string $search = '';

    public bool $showPaymentModal = false;
    public ?int $bookingId = null;
    public int $formKey = 0;

    #[On('payment-recorded')]
    public function handlePaymentRecorded(): void
    {
        $this->dispatch('toast-notify', type: 'success', message: 'Payment recorded successfully.');
        $this->closeModal();
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openModal(?int $bookingId = null): void
    {
        $this->authorize('create', Payment::class);
        $this->bookingId = $bookingId;
        $this->formKey++;
        $this->showPaymentModal = true;
    }

    public function closeModal(): void
    {
        $this->showPaymentModal = false;
        $this->bookingId = null;
    }

    public function getPaymentsProperty()
    {
        return Payment::query()
            ->with(['booking.customer'])
            ->when($this->search, function (Builder $query) {
                $term = "%{$this->search}%";
                $query->where(function (Builder $subQuery) use ($term) {
                    $subQuery
                        ->where('reference', 'like', $term)
                        ->orWhereHas('booking', function (Builder $bookingQuery) use ($term) {
                            $bookingQuery->where('reference', 'like', $term)
                                ->orWhereHas('customer', fn (Builder $customerQuery) => $customerQuery->where('name', 'like', $term));
                        });
                });
            })
            ->orderByDesc('paid_at')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.backoffice.payments.index', [
            'payments' => $this->payments,
        ]);
    }
}
