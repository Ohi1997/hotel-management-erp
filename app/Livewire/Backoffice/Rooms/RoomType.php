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

    public function mount(): void
    {
        $this->form = $this->defaults();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('room-type-saved')]
    public function handleSaved(): void
    {
        $this->resetPage();
        $this->editingId = null;
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
        $this->dispatch('toast', type: 'deleted', message: 'Room type deleted.');
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
}


