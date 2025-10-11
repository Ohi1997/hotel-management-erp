<div
    x-data="{ showModal: false }"
    x-on:close-modal.window="showModal = false"
    class="relative"
>

    <h1 class="text-2xl font-bold mb-4">Customers</h1>

    <!-- Search & Add -->
    <div class="flex justify-between mb-4">
        <input type="text" wire:model.live="search" placeholder="Search..." class="border rounded p-2 w-1/3">
        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded" @click="showModal = true">
            + Add Customer
        </button>
    </div>

    <!-- Table -->
    <table class="min-w-full bg-white border rounded shadow-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Phone</th>
                <th class="p-2 border text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td class="p-2 border">{{ $customer->name }}</td>
                    <td class="p-2 border">{{ $customer->email }}</td>
                    <td class="p-2 border">{{ $customer->phone }}</td>
                    <td class="p-2 border text-center space-x-2">
                        <button class="text-blue-600">Edit</button>
                        <button class="text-green-600">Pay Now</button>
                        <button class="text-red-600" wire:click="delete({{ $customer->id }})">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-gray-500 p-4">No customers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $customers->links() }}
    </div>

    <!-- MODAL -->
    <template x-if="showModal">
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div
                @click.away="showModal = false"
                class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl"
            >
                @php($uniqueKey = uniqid('customer_form_'))
                <livewire:backoffice.customers.form :key="$uniqueKey" />

                <div class="flex justify-end mt-4">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="bg-gray-300 px-4 py-2 rounded"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
