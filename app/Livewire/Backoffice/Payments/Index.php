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
    public bool $selectPage = false;
    public array $selected = [];

    protected $queryString = [
        'status' => ['except' => null],
        'bookingId' => ['except' => null],
        'customerId' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingBookingId(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingCustomerId(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    #[On('payment-saved')]
    public function handlePaymentSaved(): void
    {
        $this->editingPaymentId = null;
        $this->resetPage();
        $this->resetSelection();
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
        $this->removeFromSelection($paymentId);
        $this->dispatch('toast', type: 'deleted', message: 'Payment deleted.');
    }

    public function updatedSelectPage(bool $value): void
    {
        if ($value) {
            $this->selected = $this->currentPageIds();
            return;
        }

        $this->selected = [];
    }

    public function updatedSelected(): void
    {
        $currentIds = $this->currentPageIds();
        $selectedIds = $this->selectedIds();

        $this->selectPage = $currentIds !== [] && empty(array_diff($currentIds, $selectedIds));
    }

    public function deleteSelected(): void
    {
        $ids = $this->selectedIds();

        if ($ids === []) {
            return;
        }

        Payment::whereIn('id', $ids)->delete();

        $this->dispatch('toast', type: 'deleted', message: 'Selected payments deleted.');

        $this->resetSelection();
        $this->resetPage();
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

    protected function resetSelection(): void
    {
        $this->selectPage = false;
        $this->selected = [];
    }

    protected function removeFromSelection(int $id): void
    {
        $this->selected = array_values(array_filter($this->selected, fn ($selectedId) => (int) $selectedId !== $id));

        if (! $this->selected) {
            $this->selectPage = false;
        }
    }

    protected function currentPageIds(): array
    {
        return $this->payments->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    protected function selectedIds(): array
    {
        return array_map('intval', $this->selected);
    }
}


