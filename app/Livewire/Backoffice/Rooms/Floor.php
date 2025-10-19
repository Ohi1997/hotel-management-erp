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

    public function mount(): void
    {
        $this->form = $this->defaults();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
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
    }

    public function delete(int $id): void
    {
        FloorModel::findOrFail($id)->delete();
        $this->resetPage();
        $this->dispatch('toast', type: 'deleted', message: 'Floor removed.');
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
}


