<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
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

    protected $queryString = [
        'status' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    #[On('booking-saved')]
    public function handleBookingSaved(): void
    {
        $this->resetPage();
        $this->editingBookingId = null;
    }

    #[On('booking-checked-in')]
    #[On('booking-checked-out')]
    public function handleBookingStatusChange(): void
    {
        $this->resetPage();
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
}

