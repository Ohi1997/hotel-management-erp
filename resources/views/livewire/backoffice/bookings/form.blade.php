<form wire:submit.prevent="save" class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-600">Guest <span class="text-rose-500">*</span></label>
            <select
                wire:model.defer="customer_id"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            >
                <option value="">Select a customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            @error('customer_id')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Room <span class="text-rose-500">*</span></label>
            <select
                wire:model.defer="room_id"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            >
                <option value="">Select a room</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}">Room {{ $room->number }} • {{ $room->roomType?->name }} (Floor {{ $room->floor?->name ?? $room->floor?->level }})</option>
                @endforeach
            </select>
            @error('room_id')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Check-in</label>
            <input
                type="datetime-local"
                wire:model.defer="check_in_at"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            >
            @error('check_in_at')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Check-out</label>
            <input
                type="datetime-local"
                wire:model.defer="check_out_at"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            >
            @error('check_out_at')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Status</label>
            <select
                wire:model.defer="status"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            >
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="checked_in">Checked in</option>
                <option value="checked_out">Checked out</option>
                <option value="cancelled">Cancelled</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Total amount</label>
            <input
                type="number"
                min="0"
                step="0.01"
                wire:model.defer="total_amount"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            >
            @error('total_amount')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Balance due</label>
            <input
                type="number"
                min="0"
                step="0.01"
                wire:model.defer="balance_due"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            >
            @error('balance_due')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-600">Notes</label>
            <textarea
                rows="3"
                wire:model.defer="notes"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            ></textarea>
            @error('notes')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100" wire:click="$dispatch('close-modal')">Cancel</button>
        <button type="submit" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Save Booking</button>
    </div>
</form>
