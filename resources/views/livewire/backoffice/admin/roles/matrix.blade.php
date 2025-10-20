<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Role &amp; Permission Management</h1>
            <p class="text-sm text-slate-500">Assign fine-grained permissions to each role using Spatie permissions.</p>
        </div>
        <div class="flex items-center gap-3">
            <label class="text-xs font-semibold uppercase text-slate-500">Select Role</label>
            <select
                wire:model.live="selectedRoleId"
                class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
                @foreach ($roleNames as $roleId => $roleName)
                    <option value="{{ $roleId }}">{{ ucfirst($roleName) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-slate-200/70">
        <div class="border-b border-slate-200 px-4 py-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Permissions</h2>
            <p class="text-xs text-slate-500">Toggle the capabilities that should be available for the selected role.</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ($permissions as $permission)
                    <label class="flex items-center gap-3 rounded border border-slate-200 px-3 py-3 text-sm font-medium text-slate-700 hover:border-blue-400">
                        <input
                            type="checkbox"
                            value="{{ $permission['name'] }}"
                            wire:click="togglePermission('{{ $permission['name'] }}')"
                            @checked(in_array($permission['name'], $selectedPermissions))
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span>{{ ucfirst($permission['name']) }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-200 px-4 py-3">
            <button
                type="button"
                class="rounded border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                wire:click="$refresh"
            >
                Reset
            </button>
            <button
                type="button"
                wire:click="save"
                class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
                Save Permissions
            </button>
        </div>
    </div>
</div>
