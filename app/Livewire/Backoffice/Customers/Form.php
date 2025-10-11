<?php

namespace App\Livewire\Backoffice\Customers;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public $customer_id;
    public $name;
    public $email;
    public $phone;
    public $gov_id;
    public $address;
    public $notes;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'gov_id' => 'nullable|string|max:50',
        'address' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:500',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $customer = Customer::findOrFail($id);
            $this->fill($customer->toArray());
            $this->customer_id = $id;
        }
    }

    public function save()
    {
        $this->validate();

        Customer::updateOrCreate(
            ['id' => $this->customer_id],
            [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'gov_id' => $this->gov_id,
                'address' => $this->address,
                'notes' => $this->notes,
            ]
        );

        session()->flash('success', $this->customer_id ? 'Customer updated!' : 'Customer added!');
        $this->dispatch('customer-saved'); // tells parent list to refresh
        $this->reset();                    // clears the form

        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.backoffice.customers.form');
    }
}
