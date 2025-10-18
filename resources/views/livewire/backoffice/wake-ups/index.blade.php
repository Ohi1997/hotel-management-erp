<div
    x-data="{ formOpen: @entangle('showFormModal').live }"
    x-on:close-modal.window="formOpen = false"
    class="space-y-6"
>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Wake-up calls</h1>
            <p class="text-sm text-slate-500">Schedule and track guest wake-up requests.</p>
        </div>
        <button type="button" wire:click="openModal()" class="rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-purple-500">New wake-up</button>
    </div>

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="relative w-full md:w-80">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35m0 0A6.65 6.65 0 1 0 5.3 5.3a6.65 6.65 0 0 0 11.35 11.35Z" />
                </svg>
            </span>
            <input type="search" wire:model.live="search" placeholder="Search guest" class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-3 text-sm text-slate-700 shadow-sm focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500">
        </div>
        <div>
            <select wire:model.live="statusFilter" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500">
                <option value="all">All statuses</option>
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow">
        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Guest</th>
                    <th class="px-4 py-3">Schedule</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Notes</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($wakeUps as $wakeUp)
                    <tr wire:key="wake-up-{{ $wakeUp->id }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $wakeUp->customer?->name }}</div>
                            <div class="text-xs text-slate-500">{{ $wakeUp->booking?->reference ?? 'No booking' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ optional($wakeUp->scheduled_for)->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-600">{{ ucfirst($wakeUp->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $wakeUp->notes ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2 text-xs">
                                <button type="button" wire:click="openModal({{ $wakeUp->id }})" class="font-medium text-purple-600 hover:underline">Edit</button>
                                <button type="button" wire:click="markCompleted({{ $wakeUp->id }})" class="font-medium text-emerald-600 hover:underline">Complete</button>
                                <button type="button" wire:click="delete({{ $wakeUp->id }})" class="font-medium text-rose-600 hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No wake-up requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $wakeUps->onEachSide(1)->links() }}
    </div>

    <div
        x-show="formOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 py-6"
    >
        <div class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl" x-transition.scale.duration.200ms @click.away="formOpen = false; $wire.closeModal()">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-lg font-semibold text-slate-800">Wake-up</h2>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="formOpen = false; $wire.closeModal()">✕</button>
            </div>
            <div class="mt-4" wire:key="wake-up-form-{{ $formKey }}">
                <livewire:backoffice.wake-ups.form :wakeUpId="$wakeUpId" :key="'wake-up-form-'.$formKey" />
            </div>
        </div>
    </div>
</div>
