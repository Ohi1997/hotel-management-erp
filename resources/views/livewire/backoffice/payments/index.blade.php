<div
    x-data="{ paymentOpen: @entangle('showPaymentModal').live }"
    x-on:close-modal.window="paymentOpen = false"
    class="space-y-6"
>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Payments</h1>
            <p class="text-sm text-slate-500">Review and capture guest payments.</p>
        </div>
        <button
            type="button"
            wire:click="openModal()"
            class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            <span class="mr-2 text-lg">+</span>
            Record payment
        </button>
    </div>

    <div class="relative w-full md:w-80">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35m0 0A6.65 6.65 0 1 0 5.3 5.3a6.65 6.65 0 0 0 11.35 11.35Z" />
            </svg>
        </span>
        <input
            type="search"
            wire:model.live="search"
            placeholder="Search by reference or guest"
            class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        >
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow">
        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Booking</th>
                    <th class="px-4 py-3">Guest</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Method</th>
                    <th class="px-4 py-3">Paid at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($payments as $payment)
                    <tr wire:key="payment-{{ $payment->id }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $payment->booking?->reference }}</div>
                            <div class="text-xs text-slate-500">{{ $payment->reference ?? 'No reference' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $payment->booking?->customer?->name }}</div>
                            <div class="text-xs text-slate-500">{{ $payment->booking?->customer?->email }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">${{ number_format((float) $payment->amount, 2) }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $payment->method ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ optional($payment->paid_at)->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $payments->onEachSide(1)->links() }}
    </div>

    <div
        x-show="paymentOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 py-6"
    >
        <div
            class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl"
            x-transition.scale.duration.200ms
            @click.away="paymentOpen = false; $wire.closeModal()"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-lg font-semibold text-slate-800">Record Payment</h2>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="paymentOpen = false; $wire.closeModal()">✕</button>
            </div>
            <div class="mt-4" wire:key="payment-form-{{ $formKey }}">
                <livewire:backoffice.payments.add-payment :bookingId="$bookingId" :key="'add-payment-'.$formKey" />
            </div>
        </div>
    </div>
</div>
