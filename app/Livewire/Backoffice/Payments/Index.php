<?php

namespace App\Livewire\Backoffice\Payments;

use App\Models\Payment;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $status = null;
    public ?int $bookingId = null;
    public ?int $customerId = null;
    public ?int $editingPaymentId = null;
    public int $formInstance = 0;

    protected $queryString = [
        'status' => ['except' => null],
        'bookingId' => ['except' => null],
        'customerId' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingBookingId(): void
    {
        $this->resetPage();
    }

    public function updatingCustomerId(): void
    {
        $this->resetPage();
    }

    #[On('payment-saved')]
    public function handlePaymentSaved(): void
    {
        $this->editingPaymentId = null;
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->editingPaymentId = null;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'payment-form');
    }

    public function openEdit(int $paymentId): void
    {
        $this->editingPaymentId = $paymentId;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'payment-form');
    }

    public function delete(int $paymentId): void
    {
        Payment::findOrFail($paymentId)->delete();
        $this->resetPage();
        $this->dispatch('toast', type: 'deleted', message: 'Payment deleted.');
    }

    public function render()
    {
        return view('livewire.backoffice.payments.index', [
            'payments' => $this->payments,
        ])->layout('layouts.backoffice', ['pageTitle' => 'Payments']);
    }

    #[Computed]
    public function payments()
    {
        return Payment::query()
            ->with([
                'booking.room',
                'customer',
                'recordedBy',
            ])
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->bookingId, fn ($query) => $query->where('booking_id', $this->bookingId))
            ->when($this->customerId, fn ($query) => $query->where('customer_id', $this->customerId))
            ->when($this->search, function ($query): void {
                $search = '%' . $this->search . '%';
                $query->where(function ($inner) use ($search): void {
                    $inner->where('reference', 'like', $search)
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', $search))
                        ->orWhereHas('booking', fn ($q) => $q->where('reference', 'like', $search));
                });
            })
            ->latest()
            ->paginate(10);
    }
}


