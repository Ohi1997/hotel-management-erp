<form wire:submit.prevent="save" class="space-y-6">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Booking</label>
            <select
                wire:model.live="form.booking_id"
                class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            >
                <option value="">Unassigned</option>
                @foreach ($bookings as $booking)
                    <option value="{{ $booking->id }}">{{ $booking->reference }}</option>
                @endforeach
            </select>
            @error('form.booking_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Customer *</label>
            <select
                wire:model.defer="form.customer_id"
                class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            >
                <option value="">Select customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            @error('form.customer_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Scheduled for *</label>
            <input
                type="datetime-local"
                wire:model.defer="form.scheduled_for"
                class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            >
            @error('form.scheduled_for') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Completed at</label>
            <input
                type="datetime-local"
                wire:model.defer="form.completed_at"
                class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            >
            @error('form.completed_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Status *</label>
            <select
                wire:model.defer="form.status"
                class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            >
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('form.status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
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
            wire:click="$dispatch('modal-close', { id: 'wake-up-form' })"
        >
            Cancel
        </button>
        <button
            type="submit"
            class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-70"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Save</span>
            <span wire:loading>Saving…</span>
        </button>
    </div>
</form>
