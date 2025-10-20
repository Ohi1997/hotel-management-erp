<div
    x-data="{
        modals: {
            'customer-form': false,
            'customer-details': false,
        },
        open(id) { this.modals = { ...this.modals, [id]: true } },
        close(id) { this.modals = { ...this.modals, [id]: false } },
        isOpen(id) { return !!this.modals[id] }
    }"
    x-on:modal-open.window="open($event.detail.id)"
    x-on:modal-close.window="close($event.detail.id)"
    class="space-y-6"
>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Customers</h1>
        <button
            type="button"
            class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
            wire:click="openCreate"
        >
            + Add Customer
        </button>
    </div>

    <div class="bg-white border rounded-lg shadow-sm">
        <div class="flex flex-col gap-4 p-4 md:flex-row md:items-center md:justify-between">
            <input
                type="search"
                wire:model.live="search"
                placeholder="Search customers…"
                class="w-full md:w-80 border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            >

            @if ($selected)
                <div class="flex items-center gap-3 text-sm">
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Metrics</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($customers as $customer)
                        <tr wire:key="customer-row-{{ $customer->id }}">
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $customer->id }}"
                                    wire:model="selected"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                <div class="text-sm text-gray-500">ID #{{ $customer->id }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $customer->email ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $customer->phone ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-700">
                                <div class="flex items-center justify-center gap-4">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                        {{ $customer->bookings_count ?? 0 }} bookings
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        {{ $customer->payments_count ?? 0 }} payments
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        class="text-sm font-medium text-gray-600 hover:text-gray-900"
                                        wire:click="openDetails({{ $customer->id }})"
                                    >
                                        Details
                                    </button>
                                    <button
                                        type="button"
                                        class="text-sm font-medium text-blue-600 hover:text-blue-800"
                                        wire:click="openEdit({{ $customer->id }})"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="text-sm font-medium text-red-600 hover:text-red-700"
                                        x-on:click.prevent="confirm('Delete this customer?') && $wire.delete({{ $customer->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $customers->links() }}
        </div>
    </div>

    <div
        x-cloak
        x-show="isOpen('customer-form')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 px-4"
    >
        <div
            class="relative w-full max-w-2xl rounded-lg bg-white shadow-xl"
            x-transition.scale
        >
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">
                    {{ $editingCustomerId ? 'Edit Customer' : 'Add Customer' }}
                </h2>
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600"
                    x-on:click="close('customer-form')"
                >
                    ×
                </button>
            </div>

            <div class="p-6">
                <livewire:backoffice.customers.form
                    :customer-id="$editingCustomerId"
                    :key="'customer-form-' . $formInstance"
                />
            </div>
        </div>
    </div>

    <div
        x-cloak
        x-show="isOpen('customer-details')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 px-4"
    >
        <div class="relative w-full max-w-xl rounded-lg bg-white shadow-xl" x-transition.scale>
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">Customer Details</h2>
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600"
                    x-on:click="close('customer-details')"
                >
                    ×
                </button>
            </div>

            <div class="p-6">
                <livewire:backoffice.customers.show
                    :customer-id="$detailCustomerId"
                    :key="'customer-details-' . ($detailCustomerId ?? 'new')"
                />
            </div>
        </div>
    </div>
</div>
