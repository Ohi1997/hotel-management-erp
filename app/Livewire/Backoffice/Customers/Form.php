<?php

namespace App\Livewire\Backoffice\Customers;

use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

    public ?Customer $customer = null;

    public string $name = '';
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $gov_id = null;
    public ?string $address = null;
    public ?string $notes = null;

    public function mount(?int $customerId = null): void
    {
        if ($customerId) {
            $customer = Customer::findOrFail($customerId);
            $this->authorize('update', $customer);
            $this->loadCustomer($customer);
        } else {
            $this->authorize('create', Customer::class);
        }
    }

    protected function loadCustomer(?Customer $customer): void
    {
        $this->customer = $customer;

        if ($customer) {
            $this->fill($customer->only([
                'name',
                'email',
                'phone',
                'gov_id',
                'address',
                'notes',
            ]));
        } else {
            $this->reset(['name', 'email', 'phone', 'gov_id', 'address', 'notes']);
        }
    }

    public function rules(): array
    {
        $ignoreId = $this->customer?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', ValidationRule::unique('customers', 'email')->ignore($ignoreId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'gov_id' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->customer) {
            $this->authorize('update', $this->customer);
            $this->customer->update($data);
            $customer = $this->customer->refresh();
        } else {
            $this->authorize('create', Customer::class);
            $customer = Customer::create($data);
        }

        $this->loadCustomer($customer);

        $this->dispatch('customer-saved', id: $customer->id);
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.backoffice.customers.form');
    }
}
