<?php

namespace App\Livewire\Backoffice\Customers;

use App\Models\Customer;
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
        $this->authorize('viewAny', Customer::class);
    }

    #[Url(history: true)]
    public string $search = '';

    public bool $showFormModal = false;
    public bool $showDetailsModal = false;
    public ?int $editingId = null;
    public ?int $detailsId = null;
    public int $formKey = 0;

    #[On('customer-saved')]
    public function handleCustomerSaved(int $id): void
    {
        $this->resetPage();
        $this->dispatch('toast-notify', type: 'success', message: 'Customer saved successfully.');
        $this->closeFormModal();
        $this->detailsId = $id;
        $this->showDetailsModal = true;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->authorize('create', Customer::class);
        $this->editingId = null;
        $this->formKey++;
        $this->showFormModal = true;
    }

    public function openEdit(int $customerId): void
    {
        $customer = Customer::findOrFail($customerId);
        $this->authorize('update', $customer);
        $this->editingId = $customerId;
        $this->formKey++;
        $this->showFormModal = true;
    }

    public function openDetails(int $customerId): void
    {
        $customer = Customer::findOrFail($customerId);
        $this->authorize('view', $customer);
        $this->detailsId = $customerId;
        $this->showDetailsModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->detailsId = null;
    }

    public function delete(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $this->authorize('delete', $customer);
        $customer->delete();
        $this->dispatch('toast-notify', type: 'deleted', message: 'Customer deleted.');
        $this->resetPage();
    }

    public function getCustomersProperty()
    {
        return Customer::query()
            ->withCount('bookings')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $term = "%{$this->search}%";
                    $subQuery
                        ->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.backoffice.customers.index', [
            'customers' => $this->customers,
        ]);
    }
}
