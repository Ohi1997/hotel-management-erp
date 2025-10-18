<form wire:submit.prevent="save" class="space-y-5 text-sm">
    <div>
        <label class="block text-sm font-medium text-slate-600">Booking</label>
        <select
            wire:model.defer="booking_id"
            class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        >
            <option value="">Select booking</option>
            @foreach ($bookings as $booking)
                <option value="{{ $booking->id }}">{{ $booking->reference }} — {{ $booking->customer?->name }}</option>
            @endforeach
        </select>
        @error('booking_id')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-600">Amount</label>
            <input
                type="number"
                min="0"
                step="0.01"
                wire:model.defer="amount"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
            @error('amount')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Paid at</label>
            <input
                type="datetime-local"
                wire:model.defer="paid_at"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
            @error('paid_at')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-600">Method</label>
            <input
                type="text"
                wire:model.defer="method"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
            @error('method')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Reference</label>
            <input
                type="text"
                wire:model.defer="reference"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
            @error('reference')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-600">Notes</label>
        <textarea
            rows="3"
            wire:model.defer="notes"
            class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        ></textarea>
        @error('notes')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end gap-2">
        <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100" wire:click="$dispatch('close-modal')">Cancel</button>
        <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Save payment</button>
    </div>
</form>
