<?php

namespace App\Livewire\Backoffice\Customers;

use App\Models\Customer;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $editingCustomerId = null;
    public ?int $detailCustomerId = null;
    public int $formInstance = 0;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('customer-saved')]
    public function handleCustomerSaved(): void
    {
        $this->editingCustomerId = null;
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->editingCustomerId = null;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'customer-form');
    }

    public function openEdit(int $customerId): void
    {
        $this->editingCustomerId = $customerId;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'customer-form');
    }

    public function openDetails(int $customerId): void
    {
        $this->detailCustomerId = $customerId;
        $this->dispatch('modal-open', id: 'customer-details');
    }

    public function closeDetails(): void
    {
        $this->dispatch('modal-close', id: 'customer-details');
        $this->detailCustomerId = null;
    }

    public function delete(int $customerId): void
    {
        Customer::findOrFail($customerId)->delete();

        $this->resetPage();
        $this->dispatch('toast', type: 'deleted', message: 'Customer deleted successfully.');
    }

    #[Computed]
    public function customers()
    {
        return Customer::query()
            ->withCount(['bookings', 'payments'])
            ->when($this->search, function ($query): void {
                $query->where(function ($inner): void {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.backoffice.customers.index', [
            'customers' => $this->customers,
        ])
            ->layout('layouts.backoffice', ['pageTitle' => 'Customers']);
    }
}


