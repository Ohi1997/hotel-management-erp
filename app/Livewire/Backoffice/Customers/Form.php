<?php

namespace App\Livewire\Backoffice\Customers;

use App\Models\Customer;
use Livewire\Component;

class Form extends Component
{
    public ?int $customerId = null;

    /**
     * The backing form state.
     *
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(?int $customerId = null): void
    {
        $this->form = $this->defaults();
        $this->loadCustomer($customerId);
    }

    public function updatedCustomerId(?int $customerId): void
    {
        $this->loadCustomer($customerId);
    }

    public function save(): void
    {
        $validated = $this->validate();
        $payload = $validated['form'];
        $isUpdating = (bool) $this->customerId;

        $customer = Customer::updateOrCreate(
            ['id' => $this->customerId],
            $payload
        );

        $message = $isUpdating ? 'Customer updated successfully.' : 'Customer created successfully.';

        $this->dispatch('customer-saved', id: $customer->id);
        $this->dispatch('modal-close', id: 'customer-form');
        $this->dispatch('toast', type: 'success', message: $message);

        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.backoffice.customers.form');
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['nullable', 'email', 'max:255'],
            'form.phone' => ['nullable', 'string', 'max:20'],
            'form.gov_id' => ['nullable', 'string', 'max:50'],
            'form.address' => ['nullable', 'string', 'max:255'],
            'form.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function loadCustomer(?int $customerId): void
    {
        $this->customerId = $customerId;
        $this->resetValidation();
        $this->form = $this->defaults();

        if ($customerId) {
            $customer = Customer::findOrFail($customerId);
            $this->form = array_merge($this->form, $customer->only(array_keys($this->form)));
        }
    }

    protected function defaults(): array
    {
        return [
            'name' => '',
            'email' => '',
            'phone' => '',
            'gov_id' => '',
            'address' => '',
            'notes' => '',
        ];
    }

    protected function resetForm(): void
    {
        $this->customerId = null;
        $this->form = $this->defaults();
        $this->resetValidation();
    }
}
