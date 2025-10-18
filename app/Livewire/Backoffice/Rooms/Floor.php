<?php

namespace App\Livewire\Backoffice\Rooms;

use App\Models\Floor as FloorModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.backoffice')]
class Floor extends Component
{
    use AuthorizesRequests;

    public ?FloorModel $editing = null;

    public string $name = '';
    public ?string $level = null;
    public ?string $description = null;

    public function edit(int $id): void
    {
        $this->editing = FloorModel::findOrFail($id);
        $this->authorize('update', $this->editing);
        $this->fill($this->editing->only(['name', 'level', 'description']));
    }

    public function create(): void
    {
        $this->authorize('create', FloorModel::class);
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editing = null;
        $this->name = '';
        $this->level = null;
        $this->description = null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $this->authorize($this->editing ? 'update' : 'create', $this->editing ? $this->editing : FloorModel::class);
        $data = $this->validate();

        FloorModel::updateOrCreate(
            ['id' => $this->editing?->id],
            $data
        );

        $this->dispatch('toast-notify', type: 'success', message: 'Floor saved.');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $model = FloorModel::findOrFail($id);
        $this->authorize('delete', $model);
        $model->delete();
        $this->dispatch('toast-notify', type: 'deleted', message: 'Floor deleted.');
    }

    public function render()
    {
        $this->authorize('viewAny', FloorModel::class);
        return view('livewire.backoffice.rooms.floor', [
            'floors' => FloorModel::orderBy('level')->get(),
        ]);
    }
}
