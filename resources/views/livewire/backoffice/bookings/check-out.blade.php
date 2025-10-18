@if ($booking)
    <div class="space-y-4 text-sm text-slate-600">
        <div class="rounded-lg bg-slate-50 p-3 space-y-1">
            <div>Guest: <span class="font-semibold text-slate-800">{{ $booking->customer?->name }}</span></div>
            <div>Total due: ${{ number_format((float) $booking->total_amount, 2) }}</div>
            <div>Payments received: ${{ number_format((float) $paymentsTotal, 2) }}</div>
            <div>Balance remaining: <span class="font-semibold text-slate-800">${{ number_format((float) max($booking->total_amount - $paymentsTotal, 0), 2) }}</span></div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600">Checkout notes</label>
            <textarea
                rows="3"
                wire:model.defer="notes"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
            ></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100" wire:click="$dispatch('close-modal')">Cancel</button>
            <button type="button" class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-amber-400" wire:click="checkOut">Confirm checkout</button>
        </div>
    </div>
@else
    <p class="text-sm text-slate-500">Booking not found.</p>
@endif
