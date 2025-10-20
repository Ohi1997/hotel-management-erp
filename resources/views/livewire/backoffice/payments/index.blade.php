<div
    x-data="{
        modals: {},
        open(id) { this.modals[id] = true },
        close(id) { this.modals[id] = false },
        isOpen(id) { return !!this.modals[id] }
    }"
    x-on:modal-open.window="open($event.detail.id)"
    x-on:modal-close.window="close($event.detail.id)"
    class="space-y-6"
>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Payments</h1>
            <p class="text-sm text-gray-500">Track guest payments and outstanding balances.</p>
        </div>

        <button
            type="button"
            class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
            wire:click="openCreate"
        >
            + Record Payment
        </button>
    </div>

    <div class="bg-white border rounded-lg shadow-sm">
        <div class="flex flex-col gap-4 border-b p-4 md:flex-row md:items-center md:justify-between">
            <input
                type="search"
                wire:model.live="search"
                placeholder="Search reference, customer, or booking…"
                class="w-full md:w-80 rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            >

            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <select
                    wire:model.live="status"
                    class="w-full md:w-48 rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                >
                    <option value="">All statuses</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>

                @if ($selected)
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-600">{{ count($selected) }} selected</span>
                        <button
                            type="button"
                            class="rounded bg-red-600 px-3 py-2 font-medium text-white hover:bg-red-700"
                            wire:click="deleteSelected"
                            wire:loading.attr="disabled"
                        >
                            Delete Selected
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">
                            <input
                                type="checkbox"
                                wire:model="selectPage"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Booking</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($payments as $payment)
                        <tr wire:key="payment-row-{{ $payment->id }}">
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $payment->id }}"
                                    wire:model="selected"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div class="font-semibold text-gray-900">{{ $payment->reference ?? '—' }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->paid_at?->format('M d, Y H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $payment->customer?->name ?? 'Guest' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $payment->booking?->reference ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 capitalize">
                                {{ $payment->method }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ match($payment->status) {
                                    'completed' => 'bg-emerald-100 text-emerald-600',
                                    'pending' => 'bg-amber-100 text-amber-600',
                                    'failed' => 'bg-red-100 text-red-600',
                                    default => 'bg-gray-100 text-gray-600'
                                } }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                ${{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2 text-sm">
                                    <button
                                        type="button"
                                        class="text-blue-600 hover:text-blue-800"
                                        wire:click="openEdit({{ $payment->id }})"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="text-red-600 hover:text-red-700"
                                        x-on:click.prevent="confirm('Delete this payment?') && $wire.delete({{ $payment->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No payments recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t bg-gray-50 px-4 py-3">
            {{ $payments->links() }}
        </div>
    </div>

    <div
        x-cloak
        x-show="isOpen('payment-form')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
    >
        <div class="relative w-full max-w-2xl rounded-lg bg-white shadow-xl" x-transition.scale>
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">
                    {{ $editingPaymentId ? 'Edit Payment' : 'Record Payment' }}
                </h2>
                <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="close('payment-form')">×</button>
            </div>

            <div class="p-6">
                <livewire:backoffice.payments.add-payment
                    :payment-id="$editingPaymentId"
                    :key="'payments-form-' . $formInstance"
                />
            </div>
        </div>
    </div>
</div>
