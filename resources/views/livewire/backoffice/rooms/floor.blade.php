<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Floors</h1>
            <p class="text-sm text-slate-500">Define floor labels for room assignment.</p>
        </div>
        <button type="button" wire:click="create" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500">Add floor</button>
    </div>

    <form wire:submit.prevent="save" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-600">Name</label>
                <input type="text" wire:model.defer="name" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600">Level</label>
                <input type="number" wire:model.defer="level" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                @error('level')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-slate-600">Description</label>
                <textarea rows="2" wire:model.defer="description" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                @error('description')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" wire:click="create" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Cancel</button>
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500">Save</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow">
        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3">Level</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($floors as $floor)
                    <tr wire:key="floor-{{ $floor->id }}">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $floor->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $floor->level ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-500">{{ $floor->description ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button type="button" wire:click="edit({{ $floor->id }})" class="text-xs font-medium text-emerald-600 hover:underline">Edit</button>
                                <button type="button" wire:click="delete({{ $floor->id }})" class="text-xs font-medium text-rose-600 hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No floors created.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
