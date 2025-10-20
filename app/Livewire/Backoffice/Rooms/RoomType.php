<?php

namespace App\Livewire\Backoffice\Rooms;

use App\Models\RoomType as RoomTypeModel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RoomType extends Component
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

    #[On('room-type-saved')]
    public function handleSaved(): void
    {
        $this->resetPage();
        $this->editingId = null;
        $this->resetSelection();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->form = $this->defaults();
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'room-type-form');
    }

    public function edit(int $id): void
    {
        $roomType = RoomTypeModel::findOrFail($id);
        $this->editingId = $id;
        $this->form = array_merge($this->defaults(), $roomType->only(array_keys($this->defaults())));
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'room-type-form');
    }

    public function save(): void
    {
        $data = $this->validate();

        RoomTypeModel::updateOrCreate(
            ['id' => $this->editingId],
            $data['form']
        );

        $this->dispatch('room-type-saved');
        $this->dispatch('modal-close', id: 'room-type-form');
        $this->dispatch('toast', type: 'success', message: 'Room type saved.');

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        RoomTypeModel::findOrFail($id)->delete();
        $this->resetPage();
        $this->removeFromSelection($id);
        $this->dispatch('toast', type: 'deleted', message: 'Room type deleted.');
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

        RoomTypeModel::whereIn('id', $ids)->delete();

        $this->dispatch('toast', type: 'deleted', message: 'Selected room types deleted.');

        $this->resetSelection();
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.backoffice.rooms.room-type', [
            'roomTypes' => $this->roomTypes,
        ])->layout('layouts.backoffice', ['pageTitle' => 'Room Types']);
    }

    #[Computed]
    public function roomTypes()
    {
        return RoomTypeModel::query()
            ->withCount('rooms')
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(10);
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:191'],
            'form.base_rate' => ['required', 'numeric', 'min:0'],
            'form.max_occupancy' => ['required', 'integer', 'min:1', 'max:10'],
            'form.description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function defaults(): array
    {
        return [
            'name' => '',
            'base_rate' => 0,
            'max_occupancy' => 1,
            'description' => '',
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
        return $this->roomTypes->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    protected function selectedIds(): array
    {
        return array_map('intval', $this->selected);
    }
}


