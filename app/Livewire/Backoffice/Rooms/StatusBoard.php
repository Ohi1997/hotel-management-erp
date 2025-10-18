<?php

namespace App\Livewire\Backoffice\Rooms;

use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.backoffice')]
class StatusBoard extends Component
{
    use AuthorizesRequests;

    public string $statusFilter = 'all';
    public ?int $floorFilter = null;
    public ?int $typeFilter = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Room::class);
    }

    public function updateStatus(int $roomId, string $status): void
    {
        $room = Room::findOrFail($roomId);
        $this->authorize('update', $room);
        $room->update(['status' => $status]);
        $this->dispatch('toast-notify', type: 'success', message: 'Room status updated.');
    }

    public function toggleClean(int $roomId): void
    {
        $room = Room::findOrFail($roomId);
        $this->authorize('update', $room);
        $room->update(['is_clean' => ! $room->is_clean]);
        $this->dispatch('toast-notify', type: 'success', message: 'Housekeeping status updated.');
    }

    public function getRoomsProperty()
    {
        return Room::query()
            ->with(['floor', 'roomType', 'bookings' => function ($query) {
                $query->latest('check_in_at')->limit(1);
            }])
            ->when($this->statusFilter !== 'all', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->floorFilter, fn (Builder $query) => $query->where('floor_id', $this->floorFilter))
            ->when($this->typeFilter, fn (Builder $query) => $query->where('room_type_id', $this->typeFilter))
            ->orderBy('number')
            ->get();
    }

    public function render()
    {
        return view('livewire.backoffice.rooms.status-board', [
            'rooms' => $this->rooms,
            'floors' => Floor::orderBy('level')->get(),
            'roomTypes' => RoomType::orderBy('name')->get(),
        ]);
    }
}
