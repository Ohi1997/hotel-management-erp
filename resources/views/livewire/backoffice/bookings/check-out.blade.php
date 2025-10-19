<div class="space-y-4">
    @if (! $booking)
        <p class="text-center text-sm text-gray-500">Select a booking to continue.</p>
    @else
        <div class="rounded border bg-gray-50 p-4 text-sm text-gray-700">
            <div class="font-semibold text-gray-900">Booking {{ $booking->reference }}</div>
            <div class="mt-1 flex flex-wrap gap-4">
                <span>Guest: {{ $booking->customer->name }}</span>
                <span>Room {{ $booking->room->number }}</span>
                <span>Stay: {{ $booking->check_in_at?->format('M d, Y H:i') }} → {{ $booking->check_out_at?->format('M d, Y H:i') }}</span>
            </div>
        </div>

        <form wire:submit.prevent="complete" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Actual check-out *</label>
                <input
                    type="datetime-local"
                    wire:model.defer="form.actual_check_out_at"
                    class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                >
                @error('form.actual_check_out_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea
                    rows="3"
                    wire:model.defer="form.notes"
                    class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                ></textarea>
                @error('form.notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2">
                <button
                    type="button"
                    class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
                    wire:click="$dispatch('modal-close', { id: 'booking-check-out' })"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-70"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Confirm Check-out</span>
                    <span wire:loading>Processing…</span>
                </button>
            </div>
        </form>
    @endif
</div>
