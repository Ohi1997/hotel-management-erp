@if ($customer)
    <div class="space-y-6">
        <div>
            <h3 class="text-xl font-semibold text-slate-800">{{ $customer->name }}</h3>
            <div class="mt-1 text-sm text-slate-500">{{ $customer->email ?? 'No email provided' }}</div>
            <div class="text-sm text-slate-500">{{ $customer->phone ?? 'No phone provided' }}</div>
            @if ($customer->address)
                <div class="text-sm text-slate-500">{{ $customer->address }}</div>
            @endif
            @if ($customer->notes)
                <p class="mt-3 rounded-md bg-slate-50 p-3 text-sm text-slate-600">{{ $customer->notes }}</p>
            @endif
        </div>

        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Stay history</h4>
            <div class="mt-2 space-y-2">
                @forelse ($customer->bookings as $booking)
                    <div class="rounded-lg border border-slate-200 p-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-medium text-slate-800">{{ $booking->reference }}</span>
                                <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs uppercase tracking-wide text-slate-600">{{ $booking->status }}</span>
                            </div>
                            <div class="text-xs text-slate-500">{{ $booking->check_in_at?->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="mt-1 text-xs text-slate-500">
                            Room {{ $booking->room?->number }} • {{ $booking->room?->roomType?->name }}
                        </div>
                        <div class="mt-2 text-xs text-slate-500">Balance due: <span class="font-medium text-slate-700">${{ number_format((float) $booking->balance_due, 2) }}</span></div>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-200 p-4 text-center text-sm text-slate-500">No bookings yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@else
    <p class="text-center text-sm text-slate-500">Select a customer to see details.</p>
@endif
