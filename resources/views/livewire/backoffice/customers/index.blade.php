<div
    x-data="{
        formOpen: @entangle('showFormModal').live,
        detailsOpen: @entangle('showDetailsModal').live,
    }"
    x-on:close-modal.window="formOpen = false; detailsOpen = false"
    class="space-y-6"
>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Customers</h1>
            <p class="text-sm text-slate-500">Manage guest profiles, contact information, and stay history.</p>
        </div>
        <button
            type="button"
            wire:click="openCreate"
            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            <span class="mr-2 text-lg">+</span>
            New Customer
        </button>
    </div>

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="relative w-full md:w-80">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35m0 0A6.65 6.65 0 1 0 5.3 5.3a6.65 6.65 0 0 0 11.35 11.35Z" />
                </svg>
            </span>
            <input
                type="search"
                wire:model.live="search"
                placeholder="Search by name, email, or phone"
                class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-3 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
        </div>
        <div class="text-sm text-slate-500">
            Showing <span class="font-medium text-slate-700">{{ $customers->firstItem() ?? 0 }}</span>
            to <span class="font-medium text-slate-700">{{ $customers->lastItem() ?? 0 }}</span>
            of <span class="font-medium text-slate-700">{{ $customers->total() }}</span> customers
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Guest</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Bookings</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @forelse ($customers as $customer)
                    <tr wire:key="customer-{{ $customer->id }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $customer->name }}</div>
                            @if ($customer->gov_id)
                                <div class="text-xs text-slate-500">ID: {{ $customer->gov_id }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ $customer->email ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $customer->phone ?? 'No phone' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                {{ $customer->bookings_count }} stays
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-1">
                                <button
                                    type="button"
                                    wire:click="openDetails({{ $customer->id }})"
                                    class="rounded-md border border-transparent px-3 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50"
                                >
                                    Details
                                </button>
                                <button
                                    type="button"
                                    wire:click="openEdit({{ $customer->id }})"
                                    class="rounded-md border border-transparent px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $customer->id }})"
                                    class="rounded-md border border-transparent px-3 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $customers->onEachSide(1)->links() }}
    </div>

    <!-- Form Modal -->
    <div
        x-show="formOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 py-6"
    >
        <div
            class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl"
            x-transition.scale.duration.200ms
            @click.away="formOpen = false; $wire.closeFormModal()"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-lg font-semibold text-slate-800">
                    {{ $editingId ? 'Edit Customer' : 'New Customer' }}
                </h2>
                <button
                    type="button"
                    class="text-slate-400 hover:text-slate-600"
                    @click="formOpen = false; $wire.closeFormModal()"
                >
                    <span class="sr-only">Close</span>
                    ✕
                </button>
            </div>

            <div class="mt-4" wire:key="customer-form-wrapper-{{ $formKey }}">
                <livewire:backoffice.customers.form :customerId="$editingId" :key="'customer-form-'.$formKey" />
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div
        x-show="detailsOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-30 flex items-center justify-center bg-slate-900/60 px-4 py-6"
    >
        <div
            class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl"
            x-transition.scale.duration.200ms
            @click.away="detailsOpen = false; $wire.closeDetailsModal()"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-lg font-semibold text-slate-800">Customer Details</h2>
                <button
                    type="button"
                    class="text-slate-400 hover:text-slate-600"
                    @click="detailsOpen = false; $wire.closeDetailsModal()"
                >
                    <span class="sr-only">Close</span>
                    ✕
                </button>
            </div>

            <div class="mt-4" wire:key="customer-show-wrapper-{{ $detailsId ?? 'none' }}">
                <livewire:backoffice.customers.show :id="$detailsId" :key="'customer-show-'.($detailsId ?? 'new')" />
            </div>
        </div>
    </div>
</div>
