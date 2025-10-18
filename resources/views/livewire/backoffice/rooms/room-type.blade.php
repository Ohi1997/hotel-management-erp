<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Room types</h1>
            <p class="text-sm text-slate-500">Configure categories and base rates.</p>
        </div>
        <button type="button" wire:click="create" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">Add new</button>
    </div>

    <form wire:submit.prevent="save" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-600">Name</label>
                <input type="text" wire:model.defer="name" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600">Capacity</label>
                <input type="number" min="1" wire:model.defer="capacity" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('capacity')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600">Base rate</label>
                <input type="number" min="0" step="0.01" wire:model.defer="base_rate" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('base_rate')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-600">Description</label>
                <textarea rows="3" wire:model.defer="description" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                @error('description')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" wire:click="create" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Cancel</button>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">Save</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow">
        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3">Capacity</th>
                    <th class="px-4 py-3">Rate</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($types as $type)
                    <tr wire:key="room-type-{{ $type->id }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $type->name }}</div>
                            <div class="text-xs text-slate-500">{{ $type->description }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $type->capacity }}</td>
                        <td class="px-4 py-3 text-center">${{ number_format((float) $type->base_rate, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button type="button" wire:click="edit({{ $type->id }})" class="text-xs font-medium text-indigo-600 hover:underline">Edit</button>
                                <button type="button" wire:click="delete({{ $type->id }})" class="text-xs font-medium text-rose-600 hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No room types defined.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
