<?php

namespace App\Livewire\Backoffice\Rooms;

use App\Models\RoomType as RoomTypeModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.backoffice')]
class RoomType extends Component
{
    use AuthorizesRequests;

    public ?RoomTypeModel $editing = null;

    public string $name = '';
    public string $capacity = '1';
    public string $base_rate = '0';
    public ?string $description = null;

    public function edit(int $id): void
    {
        $this->editing = RoomTypeModel::findOrFail($id);
        $this->authorize('update', $this->editing);
        $this->fill($this->editing->only(['name', 'capacity', 'base_rate', 'description']));
    }

    public function create(): void
    {
        $this->authorize('create', RoomTypeModel::class);
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editing = null;
        $this->name = '';
        $this->capacity = '1';
        $this->base_rate = '0';
        $this->description = null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $this->authorize($this->editing ? 'update' : 'create', $this->editing ? $this->editing : RoomTypeModel::class);
        $data = $this->validate();

        RoomTypeModel::updateOrCreate(
            ['id' => $this->editing?->id],
            $data
        );

        $this->dispatch('toast-notify', type: 'success', message: 'Room type saved.');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $model = RoomTypeModel::findOrFail($id);
        $this->authorize('delete', $model);
        $model->delete();
        $this->dispatch('toast-notify', type: 'deleted', message: 'Room type removed.');
    }

    public function render()
    {
        $this->authorize('viewAny', RoomTypeModel::class);
        return view('livewire.backoffice.rooms.room-type', [
            'types' => RoomTypeModel::orderBy('name')->get(),
        ]);
    }
}
