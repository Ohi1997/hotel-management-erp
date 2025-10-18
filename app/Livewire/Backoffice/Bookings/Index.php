<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.backoffice')]
class Index extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Booking::class);
    }

    #[Url(history: true)]
    public string $search = '';

    #[Url(as: 'status', history: true)]
    public string $statusFilter = 'all';

    public bool $showFormModal = false;
    public bool $showCheckInModal = false;
    public bool $showCheckOutModal = false;

    public ?int $editingId = null;
    public ?int $checkInId = null;
    public ?int $checkOutId = null;

    public int $formKey = 0;

    #[On('booking-saved')]
    public function handleBookingSaved(int $id): void
    {
        $this->resetPage();
        $this->closeForm();
        $this->dispatch('toast-notify', type: 'success', message: 'Booking saved successfully.');
    }

    #[On('booking-checked-in')]
    public function handleCheckIn(): void
    {
        $this->closeCheckIn();
        $this->dispatch('toast-notify', type: 'success', message: 'Guest checked in.');
    }

    #[On('booking-checked-out')]
    public function handleCheckOut(): void
    {
        $this->closeCheckOut();
        $this->dispatch('toast-notify', type: 'success', message: 'Guest checked out.');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->authorize('create', Booking::class);
        $this->editingId = null;
        $this->formKey++;
        $this->showFormModal = true;
    }

    public function openEdit(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);
        $this->authorize('update', $booking);
        $this->editingId = $bookingId;
        $this->formKey++;
        $this->showFormModal = true;
    }

    public function openCheckIn(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);
        $this->authorize('update', $booking);
        $this->checkInId = $bookingId;
        $this->showCheckInModal = true;
    }

    public function openCheckOut(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);
        $this->authorize('update', $booking);
        $this->checkOutId = $bookingId;
        $this->showCheckOutModal = true;
    }

    public function closeForm(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;
    }

    public function closeCheckIn(): void
    {
        $this->showCheckInModal = false;
        $this->checkInId = null;
    }

    public function closeCheckOut(): void
    {
        $this->showCheckOutModal = false;
        $this->checkOutId = null;
    }

    public function delete(int $id): void
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('delete', $booking);
        $booking->delete();
        $this->dispatch('toast-notify', type: 'deleted', message: 'Booking deleted.');
        $this->resetPage();
    }

    public function getBookingsProperty()
    {
        return Booking::query()
            ->with(['customer', 'room.roomType'])
            ->withSum('payments as paid_total', 'amount')
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function (Builder $query) {
                $term = "%{$this->search}%";
                $query->where(function (Builder $subQuery) use ($term) {
                    $subQuery
                        ->where('reference', 'like', $term)
                        ->orWhereHas('customer', fn (Builder $customerQuery) => $customerQuery->where('name', 'like', $term));
                });
            })
            ->orderByDesc('check_in_at')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.index', [
            'bookings' => $this->bookings,
        ]);
    }
}
