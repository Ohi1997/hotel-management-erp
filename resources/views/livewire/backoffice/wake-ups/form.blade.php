<form wire:submit.prevent="save" class="space-y-5 text-sm">
    <div>
        <label class="block text-sm font-medium text-slate-600">Guest</label>
        <select wire:model.defer="customer_id" class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500">
            <option value="">Select customer</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>
        @error('customer_id')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-600">Booking (optional)</label>
        <select wire:model.defer="booking_id" class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500">
            <option value="">No booking</option>
            @foreach ($bookings as $booking)
                <option value="{{ $booking->id }}">{{ $booking->reference }} — {{ $booking->customer?->name }}</option>
            @endforeach
        </select>
        @error('booking_id')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-600">Scheduled for</label>
        <input type="datetime-local" wire:model.defer="scheduled_for" class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500">
        @error('scheduled_for')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-600">Status</label>
        <select wire:model.defer="status" class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500">
            <option value="scheduled">Scheduled</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        @error('status')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-600">Notes</label>
        <textarea rows="3" wire:model.defer="notes" class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500"></textarea>
        @error('notes')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end gap-2">
        <button type="button" wire:click="$dispatch('close-modal')" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Cancel</button>
        <button type="submit" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-purple-500">Save wake-up</button>
    </div>
</form>
