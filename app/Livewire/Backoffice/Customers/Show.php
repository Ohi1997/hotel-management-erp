<?php

namespace App\Livewire\Backoffice\Customers;

use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    public ?Customer $customer = null;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->loadCustomer($id);
        }
    }

    protected function loadCustomer(int $id): void
    {
        $this->customer = Customer::with(['bookings.room.roomType'])->findOrFail($id);
        $this->authorize('view', $this->customer);
    }

    public function render()
    {
        return view('livewire.backoffice.customers.show');
    }
}
