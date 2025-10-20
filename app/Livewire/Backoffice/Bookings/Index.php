<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use App\Models\Room;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $status = null;
    public ?int $editingBookingId = null;
    public ?int $checkInBookingId = null;
    public ?int $checkOutBookingId = null;
    public int $formInstance = 0;
    public int $checkInInstance = 0;
    public int $checkOutInstance = 0;
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

    #[On('booking-saved')]
    public function handleBookingSaved(): void
    {
        $this->resetPage();
        $this->editingBookingId = null;
        $this->resetSelection();
    }

    #[On('booking-checked-in')]
    #[On('booking-checked-out')]
    public function handleBookingStatusChange(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function openCreate(): void
    {
        $this->editingBookingId = null;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'booking-form');
    }

    public function openEdit(int $bookingId): void
    {
        $this->editingBookingId = $bookingId;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'booking-form');
    }

    public function openCheckIn(int $bookingId): void
    {
        $this->checkInBookingId = $bookingId;
        $this->checkInInstance++;
        $this->dispatch('modal-open', id: 'booking-check-in');
    }

    public function openCheckOut(int $bookingId): void
    {
        $this->checkOutBookingId = $bookingId;
        $this->checkOutInstance++;
        $this->dispatch('modal-open', id: 'booking-check-out');
    }

    public function delete(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);
        $room = $booking->room;

        $booking->delete();

        if ($room) {
            $room->update(['status' => Room::STATUS_AVAILABLE]);
        }

        $this->resetPage();
        $this->removeFromSelection($bookingId);
        $this->dispatch('toast', type: 'deleted', message: 'Booking deleted.');
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
        $selectedIds = array_map('intval', $this->selected);

        $this->selectPage = $currentIds !== [] && empty(array_diff($currentIds, $selectedIds));
    }

    public function deleteSelected(): void
    {
        if (! $this->selected) {
            return;
        }

        $bookings = Booking::with('room')->whereIn('id', $this->selected)->get();

        foreach ($bookings as $booking) {
            $booking->delete();
            $booking->room?->update(['status' => Room::STATUS_AVAILABLE]);
        }

        $this->dispatch('toast', type: 'deleted', message: 'Selected bookings deleted.');

        $this->resetSelection();
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.index', [
            'bookings' => $this->bookings,
        ])->layout('layouts.backoffice', ['pageTitle' => 'Bookings']);
    }

    #[Computed]
    public function bookings()
    {
        return Booking::query()
            ->with([
                'customer',
                'room.roomType',
                'room.floor',
            ])
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->search, function ($query): void {
                $search = '%' . $this->search . '%';
                $query->where(function ($inner) use ($search): void {
                    $inner->where('reference', 'like', $search)
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', $search))
                        ->orWhereHas('room', fn ($q) => $q->where('number', 'like', $search));
                });
            })
            ->orderByDesc('check_in_at')
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
        return $this->bookings->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}

