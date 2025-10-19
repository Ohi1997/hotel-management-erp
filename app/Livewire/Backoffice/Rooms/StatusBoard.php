<?php

namespace App\Livewire\Backoffice\Rooms;

use App\Models\Floor as FloorModel;
use App\Models\Room;
use App\Models\RoomType as RoomTypeModel;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StatusBoard extends Component
{
    public ?string $status = null;
    public ?int $floorId = null;
    public ?int $roomTypeId = null;

    protected $queryString = [
        'status' => ['except' => null],
        'floorId' => ['except' => null],
        'roomTypeId' => ['except' => null],
    ];

    public function updatingStatus(): void
    {
        // simply trigger re-render
    }

    public function updatingFloorId(): void
    {
        // simply trigger re-render
    }

    public function updatingRoomTypeId(): void
    {
        // simply trigger re-render
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status ?: null;
    }

    public function render()
    {
        return view('livewire.backoffice.rooms.status-board', [
            'rooms' => $this->rooms,
            'floors' => FloorModel::orderBy('level')->orderBy('name')->get(),
            'roomTypes' => RoomTypeModel::orderBy('name')->get(),
            'statusOptions' => $this->statusOptions(),
        ])->layout('layouts.backoffice', ['pageTitle' => 'Room Status Board']);
    }

    #[Computed]
    public function rooms()
    {
        return Room::query()
            ->with([
                'roomType',
                'floor',
                'bookings' => fn ($query) => $query->latest()->limit(1)->with('customer'),
            ])
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->floorId, fn ($query) => $query->where('floor_id', $this->floorId))
            ->when($this->roomTypeId, fn ($query) => $query->where('room_type_id', $this->roomTypeId))
            ->orderBy('number')
            ->get();
    }

    protected function statusOptions(): array
    {
        return [
            null => 'All Rooms',
            Room::STATUS_AVAILABLE => 'Available',
            Room::STATUS_OCCUPIED => 'Occupied',
            Room::STATUS_CLEANING => 'Cleaning',
            Room::STATUS_MAINTENANCE => 'Maintenance',
            Room::STATUS_OUT_OF_SERVICE => 'Out of Service',
        ];
    }
}

