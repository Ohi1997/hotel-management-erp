<?php

namespace App\Livewire\Backoffice\Rooms;

use App\Models\Floor as FloorModel;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Floor extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $editingId = null;
    public array $form = [];
    public int $formInstance = 0;
    public bool $selectPage = false;
    public array $selected = [];

    public function mount(): void
    {
        $this->form = $this->defaults();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->form = $this->defaults();
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'floor-form');
    }

    public function edit(int $id): void
    {
        $floor = FloorModel::findOrFail($id);
        $this->editingId = $id;
        $this->form = array_merge($this->defaults(), $floor->only(array_keys($this->defaults())));
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'floor-form');
    }

    public function save(): void
    {
        $data = $this->validate();

        FloorModel::updateOrCreate(
            ['id' => $this->editingId],
            $data['form']
        );

        $this->dispatch('toast', type: 'success', message: 'Floor saved.');
        $this->dispatch('modal-close', id: 'floor-form');

        $this->resetForm();
        $this->resetPage();
        $this->resetSelection();
    }

    public function delete(int $id): void
    {
        FloorModel::findOrFail($id)->delete();
        $this->resetPage();
        $this->removeFromSelection($id);
        $this->dispatch('toast', type: 'deleted', message: 'Floor removed.');
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

        FloorModel::whereIn('id', $ids)->delete();

        $this->dispatch('toast', type: 'deleted', message: 'Selected floors removed.');

        $this->resetSelection();
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.backoffice.rooms.floor', [
            'floors' => $this->floors,
        ])->layout('layouts.backoffice', ['pageTitle' => 'Floors']);
    }

    #[Computed]
    public function floors()
    {
        return FloorModel::query()
            ->withCount('rooms')
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('level')
            ->paginate(10);
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:191'],
            'form.level' => ['nullable', 'integer', 'min:-5', 'max:100'],
            'form.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'name' => '',
            'level' => null,
            'notes' => '',
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = $this->defaults();
        $this->resetValidation();
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
        return $this->floors->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    protected function selectedIds(): array
    {
        return array_map('intval', $this->selected);
    }
}


