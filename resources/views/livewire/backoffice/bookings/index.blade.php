<div
    x-data="{
        formOpen: @entangle('showFormModal').live,
        checkInOpen: @entangle('showCheckInModal').live,
        checkOutOpen: @entangle('showCheckOutModal').live,
    }"
    x-on:close-modal.window="formOpen = false; checkInOpen = false; checkOutOpen = false"
    class="space-y-6"
>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Bookings</h1>
            <p class="text-sm text-slate-500">Track reservations, arrivals, and departures.</p>
        </div>
        <button
            type="button"
            wire:click="openCreate"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
        >
            <span class="mr-2 text-lg">+</span>
            New Booking
        </button>
    </div>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="relative w-full lg:w-96">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35m0 0A6.65 6.65 0 1 0 5.3 5.3a6.65 6.65 0 0 0 11.35 11.35Z" />
                </svg>
            </span>
            <input
                type="search"
                wire:model.live="search"
                placeholder="Search by reference or guest name"
                class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-3 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            >
        </div>
        <div class="flex flex-wrap items-center gap-2 text-sm">
            @php($statuses = ['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'checked_in' => 'Checked in', 'checked_out' => 'Checked out', 'cancelled' => 'Cancelled'])
            @foreach ($statuses as $value => $label)
                <button
                    type="button"
                    wire:click="$set('statusFilter', '{{ $value }}')"
                    class="rounded-full border px-3 py-1 {{ $statusFilter === $value ? 'border-emerald-500 bg-emerald-50 text-emerald-600' : 'border-slate-200 text-slate-600 hover:bg-slate-100' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow">
        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Reference</th>
                    <th class="px-4 py-3">Guest</th>
                    <th class="px-4 py-3">Room</th>
                    <th class="px-4 py-3">Dates</th>
                    <th class="px-4 py-3">Financials</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($bookings as $booking)
                    <tr wire:key="booking-{{ $booking->id }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $booking->reference }}</div>
                            <div class="mt-1 text-xs uppercase tracking-wide text-slate-500">{{ ucfirst($booking->status) }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $booking->customer?->name }}</div>
                            <div class="text-xs text-slate-500">{{ $booking->customer?->email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div>Room {{ $booking->room?->number }}</div>
                            <div class="text-xs text-slate-500">{{ $booking->room?->roomType?->name }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">
                            <div>Arrive: {{ optional($booking->check_in_at)->format('M d, Y H:i') }}</div>
                            <div>Depart: {{ optional($booking->check_out_at)->format('M d, Y H:i') ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">
                            <div>Total: <span class="font-semibold text-slate-700">${{ number_format((float) $booking->total_amount, 2) }}</span></div>
                            <div>Paid: ${{ number_format((float) ($booking->paid_total ?? 0), 2) }}</div>
                            <div>Balance: <span class="font-semibold text-slate-700">${{ number_format((float) $booking->balance_due, 2) }}</span></div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <button
                                    type="button"
                                    wire:click="openEdit({{ $booking->id }})"
                                    class="rounded-md border border-transparent px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100"
                                >Edit</button>
                                @if ($booking->status === 'confirmed')
                                    <button
                                        type="button"
                                        wire:click="openCheckIn({{ $booking->id }})"
                                        class="rounded-md border border-transparent px-3 py-1 text-xs font-medium text-emerald-600 hover:bg-emerald-50"
                                    >Check in</button>
                                @endif
                                @if ($booking->status === 'checked_in')
                                    <button
                                        type="button"
                                        wire:click="openCheckOut({{ $booking->id }})"
                                        class="rounded-md border border-transparent px-3 py-1 text-xs font-medium text-amber-600 hover:bg-amber-50"
                                    >Check out</button>
                                @endif
                                <button
                                    type="button"
                                    wire:click="delete({{ $booking->id }})"
                                    class="rounded-md border border-transparent px-3 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50"
                                >Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No bookings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $bookings->onEachSide(1)->links() }}
    </div>

    <!-- Booking Form Modal -->
    <div
        x-show="formOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 py-6"
    >
        <div
            class="w-full max-w-3xl rounded-xl bg-white p-6 shadow-xl"
            x-transition.scale.duration.200ms
            @click.away="formOpen = false; $wire.closeForm()"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-lg font-semibold text-slate-800">{{ $editingId ? 'Edit Booking' : 'Create Booking' }}</h2>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="formOpen = false; $wire.closeForm()">✕</button>
            </div>
            <div class="mt-4" wire:key="booking-form-wrapper-{{ $formKey }}">
                <livewire:backoffice.bookings.form :bookingId="$editingId" :key="'booking-form-'.$formKey" />
            </div>
        </div>
    </div>

    <!-- Check-in Modal -->
    <div
        x-show="checkInOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 py-6"
    >
        <div
            class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl"
            x-transition.scale.duration.200ms
            @click.away="checkInOpen = false; $wire.closeCheckIn()"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-lg font-semibold text-slate-800">Check In</h2>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="checkInOpen = false; $wire.closeCheckIn()">✕</button>
            </div>
            <div class="mt-4" wire:key="booking-check-in-{{ $checkInId ?? 'none' }}">
                <livewire:backoffice.bookings.check-in :bookingId="$checkInId" :key="'booking-check-in-'.($checkInId ?? 'new')" />
            </div>
        </div>
    </div>

    <!-- Check-out Modal -->
    <div
        x-show="checkOutOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 py-6"
    >
        <div
            class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl"
            x-transition.scale.duration.200ms
            @click.away="checkOutOpen = false; $wire.closeCheckOut()"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-lg font-semibold text-slate-800">Check Out</h2>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="checkOutOpen = false; $wire.closeCheckOut()">✕</button>
            </div>
            <div class="mt-4" wire:key="booking-check-out-{{ $checkOutId ?? 'none' }}">
                <livewire:backoffice.bookings.check-out :bookingId="$checkOutId" :key="'booking-check-out-'.($checkOutId ?? 'new')" />
            </div>
        </div>
    </div>
</div>
