<div
    x-data="{
        modals: {
            'room-type-form': false,
        },
        open(id) { this.modals = { ...this.modals, [id]: true } },
        close(id) { this.modals = { ...this.modals, [id]: false } },
        isOpen(id) { return !!this.modals[id] }
    }"
    x-on:modal-open.window="open($event.detail.id)"
    x-on:modal-close.window="close($event.detail.id)"
    class="space-y-6"
>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Room Types</h1>
            <p class="text-sm text-gray-500">Define templates for rooms including rates and occupancy.</p>
        </div>

        <button
            type="button"
            class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
            wire:click="create"
        >
            + New Room Type
        </button>
    </div>

    <div class="bg-white border rounded-lg shadow-sm">
        <div class="flex flex-col gap-4 border-b p-4 md:flex-row md:items-center md:justify-between">
            <input
                type="search"
                wire:model.live="search"
                placeholder="Search room types…"
                class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 md:w-80"
            >

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
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Base Rate</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Max Occupancy</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Rooms</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($roomTypes as $type)
                        <tr wire:key="room-type-{{ $type->id }}">
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $type->id }}"
                                    wire:model="selected"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                {{ $type->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                ${{ number_format($type->base_rate, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $type->max_occupancy }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $type->rooms_count }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $type->description ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        class="text-blue-600 hover:text-blue-800"
                                        wire:click="edit({{ $type->id }})"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="text-red-600 hover:text-red-700"
                                        x-on:click.prevent="confirm('Delete this room type?') && $wire.delete({{ $type->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No room types defined yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t bg-gray-50 px-4 py-3">
            {{ $roomTypes->links() }}
        </div>
    </div>

    <div
        x-cloak
        x-show="isOpen('room-type-form')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
    >
        <div class="relative w-full max-w-xl rounded-lg bg-white shadow-xl" x-transition.scale>
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">
                    {{ $editingId ? 'Edit Room Type' : 'Create Room Type' }}
                </h2>
                <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="close('room-type-form')">×</button>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name *</label>
                        <input
                            type="text"
                            wire:model.defer="form.name"
                            class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                        >
                        @error('form.name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Base Rate *</label>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                wire:model.defer="form.base_rate"
                                class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                            >
                            @error('form.base_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Max Occupancy *</label>
                            <input
                                type="number"
                                min="1"
                                max="10"
                                wire:model.defer="form.max_occupancy"
                                class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                            >
                            @error('form.max_occupancy') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            rows="3"
                            wire:model.defer="form.description"
                            class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                        ></textarea>
                        @error('form.description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="button"
                            class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
                            wire:click="$dispatch('modal-close', { id: 'room-type-form' })"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-70"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Save</span>
                            <span wire:loading>Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
