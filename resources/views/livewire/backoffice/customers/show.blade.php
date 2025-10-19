<div class="space-y-6">
    @if (! $customer)
        <p class="text-center text-gray-500">Select a customer to view their details.</p>
    @else
        <section class="space-y-4">
            <header>
                <h3 class="text-lg font-semibold text-gray-900">{{ $customer->name }}</h3>
                <p class="text-sm text-gray-500">Joined {{ $customer->created_at?->format('M d, Y') }}</p>
            </header>

            <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg border p-4">
                    <dt class="text-xs font-medium uppercase text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-900">{{ $customer->email ?? '—' }}</dd>
                </div>
                <div class="rounded-lg border p-4">
                    <dt class="text-xs font-medium uppercase text-gray-500">Phone</dt>
                    <dd class="text-sm text-gray-900">{{ $customer->phone ?? '—' }}</dd>
                </div>
                <div class="rounded-lg border p-4">
                    <dt class="text-xs font-medium uppercase text-gray-500">Government ID</dt>
                    <dd class="text-sm text-gray-900">{{ $customer->gov_id ?? '—' }}</dd>
                </div>
                <div class="rounded-lg border p-4">
                    <dt class="text-xs font-medium uppercase text-gray-500">Address</dt>
                    <dd class="text-sm text-gray-900">{{ $customer->address ?? '—' }}</dd>
                </div>
            </dl>

            @if ($customer->notes)
                <div class="rounded-lg border bg-amber-50 p-4 text-sm text-amber-800">
                    {{ $customer->notes }}
                </div>
            @endif
        </section>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Recent Bookings</h4>
                    <span class="text-xs text-gray-500">{{ $customer->bookings_count }} total</span>
                </div>

                <div class="space-y-3">
                    @forelse ($customer->bookings->take(5) as $booking)
                        <div class="rounded-lg border p-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-gray-900">#{{ $booking->reference }}</span>
                                <span class="text-xs uppercase text-gray-500">{{ str_replace('_', ' ', $booking->status) }}</span>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                <span>{{ $booking->check_in_at?->format('M d') }} → {{ $booking->check_out_at?->format('M d, Y') }}</span>
                                @if ($booking->room)
                                    <span class="ml-2 text-gray-700">{{ $booking->room->number }} · {{ $booking->room->roomType?->name }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No bookings recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Recent Payments</h4>
                    <span class="text-xs text-gray-500">{{ $customer->payments_count }} total</span>
                </div>

                <div class="space-y-3">
                    @forelse ($customer->payments->take(5) as $payment)
                        <div class="rounded-lg border p-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-gray-900">
                                    ${{ number_format($payment->amount, 2) }}
                                </span>
                                <span class="text-xs uppercase text-gray-500">{{ $payment->method }}</span>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                Paid {{ $payment->paid_at?->format('M d, Y H:i') ?? '—' }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No payments recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif
</div>
