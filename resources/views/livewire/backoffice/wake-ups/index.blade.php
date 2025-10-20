<div
    x-data="{
        modals: {
            'wake-up-form': false,
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
            <h1 class="text-2xl font-semibold text-gray-900">Wake-Up Calls</h1>
            <p class="text-sm text-gray-500">Schedule reminders and follow-ups for guests.</p>
        </div>

        <button
            type="button"
            class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
            wire:click="openCreate"
        >
            + Schedule Wake-Up
        </button>
    </div>

    <div class="bg-white border rounded-lg shadow-sm">
        <div class="flex flex-col gap-4 border-b p-4 md:flex-row md:items-center md:justify-between">
            <input
                type="search"
                wire:model.live="search"
                placeholder="Search guest or notes…"
                class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 md:w-80"
            >

            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <select
                    wire:model.live="status"
                    class="w-full md:w-48 rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                >
                    <option value="">All statuses</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
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
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Guest</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Booking</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Scheduled For</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Notes</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($wakeUps as $wakeUp)
                        <tr wire:key="wake-up-{{ $wakeUp->id }}">
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $wakeUp->id }}"
                                    wire:model="selected"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div class="font-semibold text-gray-900">{{ $wakeUp->customer?->name ?? 'Guest' }}</div>
                                <div class="text-xs text-gray-500">{{ $wakeUp->customer?->phone ?? $wakeUp->customer?->email ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $wakeUp->booking?->reference ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $wakeUp->scheduled_for?->format('M d, Y H:i') }}
                                @if ($wakeUp->completed_at)
                                    <div class="text-xs text-gray-500">Completed {{ $wakeUp->completed_at->format('M d, H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ match($wakeUp->status) {
                                    'scheduled' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-gray-200 text-gray-700',
                                    default => 'bg-gray-100 text-gray-700'
                                } }}">
                                    {{ ucfirst($wakeUp->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $wakeUp->notes ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        class="text-blue-600 hover:text-blue-800"
                                        wire:click="openEdit({{ $wakeUp->id }})"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="text-red-600 hover:text-red-700"
                                        x-on:click.prevent="confirm('Delete this wake-up?') && $wire.delete({{ $wakeUp->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No wake-up calls scheduled.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t bg-gray-50 px-4 py-3">
            {{ $wakeUps->links() }}
        </div>
    </div>

    <div
        x-cloak
        x-show="isOpen('wake-up-form')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
    >
        <div class="relative w-full max-w-xl rounded-lg bg-white shadow-xl" x-transition.scale>
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold">
                    {{ $editingId ? 'Edit Wake-Up' : 'Schedule Wake-Up' }}
                </h2>
                <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="close('wake-up-form')">×</button>
            </div>

            <div class="p-6">
                <livewire:backoffice.wake-ups.form
                    :wake-up-id="$editingId"
                    :key="'wake-up-form-' . $formInstance"
                />
            </div>
        </div>
    </div>
</div>
