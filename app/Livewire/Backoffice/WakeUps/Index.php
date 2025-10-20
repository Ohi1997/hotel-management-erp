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
    public bool $selectPage = false;
    public array $selected = [];

    protected $queryString = [
        'status' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    #[On('wake-up-saved')]
    public function handleSaved(): void
    {
        $this->resetPage();
        $this->editingId = null;
        $this->resetSelection();
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
        $this->removeFromSelection($id);
        $this->dispatch('toast', type: 'deleted', message: 'Wake-up removed.');
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
        $selectedIds = $this->selectedIds();

        $this->selectPage = $currentIds !== [] && empty(array_diff($currentIds, $selectedIds));
    }

    public function deleteSelected(): void
    {
        $ids = $this->selectedIds();

        if ($ids === []) {
            return;
        }

        WakeUp::whereIn('id', $ids)->delete();

        $this->dispatch('toast', type: 'deleted', message: 'Selected wake-up requests removed.');

        $this->resetSelection();
        $this->resetPage();
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
        return $this->wakeUps->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    protected function selectedIds(): array
    {
        return array_map('intval', $this->selected);
    }
}


