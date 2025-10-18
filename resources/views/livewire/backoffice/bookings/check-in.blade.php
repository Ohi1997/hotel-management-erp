@if ($booking)
    <div class="space-y-4 text-sm text-slate-600">
        <p>Confirm check-in for <span class="font-semibold text-slate-800">{{ $booking->customer?->name }}</span> in room {{ $booking->room?->number }}.</p>
        <div class="rounded-lg bg-slate-50 p-3">
            <div>Scheduled arrival: {{ optional($booking->check_in_at)->format('M d, Y H:i') }}</div>
            <div>Status: <span class="font-semibold text-slate-800">{{ ucfirst($booking->status) }}</span></div>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100" wire:click="$dispatch('close-modal')">Cancel</button>
            <button type="button" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-emerald-500" wire:click="checkIn">Confirm check-in</button>
        </div>
    </div>
@else
    <p class="text-sm text-slate-500">Booking not found.</p>
@endif
