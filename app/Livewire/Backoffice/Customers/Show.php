<?php

namespace App\Livewire\Backoffice\Customers;

use App\Models\Customer;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public ?int $customerId = null;

    #[Computed]
    public function customer(): ?Customer
    {
        if (! $this->customerId) {
            return null;
        }

        return Customer::query()
            ->with([
                'bookings.room.roomType',
                'bookings.room.floor',
                'payments' => fn ($query) => $query->latest(),
            ])
            ->withCount([
                'bookings',
                'payments',
            ])
            ->find($this->customerId);
    }

    public function render()
    {
        return view('livewire.backoffice.customers.show', [
            'customer' => $this->customer,
        ]);
    }
}
