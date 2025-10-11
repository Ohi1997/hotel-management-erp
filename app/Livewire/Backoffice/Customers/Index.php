<?php

namespace App\Livewire\Backoffice\Customers;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $listeners = ['customer-saved' => '$refresh'];

    public $search = '';

    public function updatingSearch() {
        $this->resetPage();
    }

    public function delete($id)
    {
        Customer::findOrFail($id)->delete();
        session()->flash('success', 'Customer deleted successfully!');
    }

    public function render()
    {
        $customers = Customer::query()
            ->where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.backoffice.customers.index', compact('customers'))
            ->layout('layouts.backoffice');
    }
}
