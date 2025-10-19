<?php

namespace App\Livewire\Backoffice\WakeUps;

use App\Models\WakeUp;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $status = null;
    public ?int $editingId = null;
    public int $formInstance = 0;

    protected $queryString = [
        'status' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    #[On('wake-up-saved')]
    public function handleSaved(): void
    {
        $this->resetPage();
        $this->editingId = null;
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'wake-up-form');
    }

    public function openEdit(int $id): void
    {
        $this->editingId = $id;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'wake-up-form');
    }

    public function delete(int $id): void
    {
        WakeUp::findOrFail($id)->delete();
        $this->resetPage();
        $this->dispatch('toast', type: 'deleted', message: 'Wake-up removed.');
    }

    public function render()
    {
        return view('livewire.backoffice.wake-ups.index', [
            'wakeUps' => $this->wakeUps,
        ])->layout('layouts.backoffice', ['pageTitle' => 'Wake-Up Calls']);
    }

    #[Computed]
    public function wakeUps()
    {
        return WakeUp::query()
            ->with(['booking.room', 'customer'])
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->search, function ($query): void {
                $search = '%' . $this->search . '%';
                $query->where(function ($inner) use ($search): void {
                    $inner->whereHas('customer', fn ($q) => $q->where('name', 'like', $search))
                        ->orWhere('notes', 'like', $search);
                });
            })
            ->orderBy('scheduled_for')
            ->paginate(10);
    }
}


