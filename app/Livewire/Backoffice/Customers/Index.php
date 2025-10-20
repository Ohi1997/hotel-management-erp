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
    public bool $selectPage = false;
    public array $selected = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    #[On('customer-saved')]
    public function handleCustomerSaved(): void
    {
        $this->editingCustomerId = null;
        $this->resetPage();
        $this->resetSelection();
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
        $this->removeFromSelection($customerId);
        $this->dispatch('toast', type: 'deleted', message: 'Customer deleted successfully.');
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
        $selectedIds = array_map('intval', $this->selected);

        $this->selectPage = $currentIds !== [] && empty(array_diff($currentIds, $selectedIds));
    }

    public function deleteSelected(): void
    {
        if (! $this->selected) {
            return;
        }

        Customer::whereIn('id', $this->selected)->delete();

        $this->dispatch('toast', type: 'deleted', message: 'Selected customers deleted successfully.');

        $this->resetSelection();
        $this->resetPage();
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
        return $this->customers->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}


