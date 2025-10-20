<div
    x-data="{
        modals: {
            'booking-form': false,
            'booking-check-in': false,
            'booking-check-out': false,
        },
        open(id) { this.modals = { ...this.modals, [id]: true } },
        close(id) { this.modals = { ...this.modals, [id]: false } },
        isOpen(id) { return !!this.modals[id] }
    }"
    x-on:modal-open.window="open($event.detail.id)"
    x-on:modal-close.window="close($event.detail.id)"
    class="space-y-6"
>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Bookings</h1>
            <p class="text-sm text-gray-500">Manage reservations, check-ins, and check-outs.</p>
        </div>

        <div class="flex gap-2">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                wire:click="openCreate"
            >
                + New Booking
            </button>
        </div>
    </div>

    <div class="bg-white border rounded-lg shadow-sm">
        <div class="flex flex-col gap-4 border-b p-4 md:flex-row md:items-center md:justify-between">
            <div class="flex w-full flex-col gap-3 md:flex-row md:items-center">
                <input
                    type="search"
                    wire:model.live="search"
                    placeholder="Search reference, customer, or room…"
                    class="w-full md:w-80 rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                >

                <select
                    wire:model.live="status"
                    class="w-full md:w-48 rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                >
                    <option value="">All statuses</option>
                    <option value="reserved">Reserved</option>
                    <option value="checked_in">Checked in</option>
                    <option value="checked_out">Checked out</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            @if ($selected)
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-600">{{ count($selected) }} selected</span>
                    <button
                        type="button"
                        class="rounded bg-red-600 px-3 py-2 font-medium text-white hover:bg-red-700"
                        wire:click="deleteSelected"
                        wire:loading.attr="disabled"
                    >
                        Delete Selected
                    </button>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">
                            <input
                                type="checkbox"
                                wire:model.live="selectPage"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Guest</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Room</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Stay</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($bookings as $booking)
                        <tr wire:key="booking-row-{{ $booking->id }}">
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $booking->id }}"
                                    wire:model.live="selected"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                {{ $booking->reference }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div>{{ $booking->customer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->customer->email ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div class="font-medium text-gray-900">Room {{ $booking->room->number }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $booking->room->roomType?->name }} · Floor {{ $booking->room->floor?->name }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div>
                                    {{ $booking->check_in_at?->format('M d, Y H:i') }} →
                                    {{ $booking->check_out_at?->format('M d, Y H:i') }}
                                </div>
                                <div class="text-xs text-gray-500">Guests: {{ $booking->guest_count }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ match($booking->status) {
                                    'reserved' => 'bg-blue-100 text-blue-700',
                                    'checked_in' => 'bg-emerald-100 text-emerald-700',
                                    'checked_out' => 'bg-gray-200 text-gray-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                } }}">
                                    {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                ${{ number_format($booking->total_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-wrap justify-end gap-2 text-sm">
                                    <button
                                        type="button"
                                        class="text-gray-600 hover:text-gray-900"
                                        wire:click="openEdit({{ $booking->id }})"
                                    >
                                        Edit
                                    </button>

                                    @if ($booking->status === 'reserved')
                                        <button
                                            type="button"
                                            class="text-emerald-600 hover:text-emerald-800"
                                            wire:click="openCheckIn({{ $booking->id }})"
                                        >
                                            Check In
                                        </button>
                                    @elseif ($booking->status === 'checked_in')
                                        <button
                                            type="button"
                                            class="text-blue-600 hover:text-blue-800"
                                            wire:click="openCheckOut({{ $booking->id }})"
                                        >
                                            Check Out
                                        </button>
                                    @endif

                                    <button
                                        type="button"
                                        class="text-red-600 hover:text-red-700"
                                        x-on:click.prevent="confirm('Delete this booking?') && $wire.delete({{ $booking->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t bg-gray-50 px-4 py-3">
            {{ $bookings->links() }}
        </div>
    </div>

    <div
        x-cloak
        x-show="isOpen('booking-form')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
    >
        <div class="relative w-full max-w-3xl rounded-lg bg-white shadow-xl" x-transition.scale>
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">
                    {{ $editingBookingId ? 'Edit Booking' : 'Create Booking' }}
                </h2>
                <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="close('booking-form')">×</button>
            </div>

            <div class="p-6">
                <livewire:backoffice.bookings.form
                    :booking-id="$editingBookingId"
                    :key="'booking-form-' . $formInstance"
                />
            </div>
        </div>
    </div>

    <div
        x-cloak
        x-show="isOpen('booking-check-in')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
    >
        <div class="relative w-full max-w-xl rounded-lg bg-white shadow-xl" x-transition.scale>
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">Check In</h2>
                <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="close('booking-check-in')">×</button>
            </div>

            <div class="p-6">
                <livewire:backoffice.bookings.check-in
                    :booking-id="$checkInBookingId"
                    :key="'booking-check-in-' . $checkInInstance"
                />
            </div>
        </div>
    </div>

    <div
        x-cloak
        x-show="isOpen('booking-check-out')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
    >
        <div class="relative w-full max-w-xl rounded-lg bg-white shadow-xl" x-transition.scale>
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">Check Out</h2>
                <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="close('booking-check-out')">×</button>
            </div>

            <div class="p-6">
                <livewire:backoffice.bookings.check-out
                    :booking-id="$checkOutBookingId"
                    :key="'booking-check-out-' . $checkOutInstance"
                />
            </div>
        </div>
    </div>
</div>
