<div
    x-data="{
        modals: {
            'user-form': false,
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
            <h1 class="text-2xl font-bold">User Management</h1>
            <p class="text-sm text-slate-500">Create, update, and deactivate system users.</p>
        </div>
        <button
            type="button"
            class="inline-flex items-center rounded bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700"
            wire:click="openCreate"
        >
            + Add User
        </button>
    </div>

    <div class="bg-white shadow-sm ring-1 ring-slate-200/70 rounded-lg">
        <div class="border-b border-slate-200 p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Search</label>
                    <input
                        type="search"
                        wire:model.live="search"
                        placeholder="Search by name or email"
                        class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Role</label>
                    <select
                        wire:model.live="role"
                        class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    >
                        <option value="all">All roles</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Status</label>
                    <select
                        wire:model.live="status"
                        class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    >
                        <option value="all">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Hotel</label>
                    <select
                        wire:model.live="filterHotelId"
                        class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    >
                        <option value="">All hotels</option>
                        @foreach ($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">User</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Hotel</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr wire:key="user-row-{{ $user->id }}">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @forelse ($user->roles as $userRole)
                                        <span class="inline-flex rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                            {{ ucfirst($userRole->name) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400">No role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $user->hotel?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-700' => $user->status,
                                    'bg-rose-50 text-rose-700' => ! $user->status,
                                ])>
                                    {{ $user->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-3 text-xs font-semibold">
                                    <button
                                        type="button"
                                        class="text-blue-600 hover:text-blue-800"
                                        wire:click="openEdit({{ $user->id }})"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="text-slate-600 hover:text-slate-900"
                                        wire:click="resetPassword({{ $user->id }})"
                                    >
                                        Reset Password
                                    </button>
                                    <button
                                        type="button"
                                        class="text-rose-600 hover:text-rose-700"
                                        wire:click="toggleStatus({{ $user->id }})"
                                    >
                                        {{ $user->status ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $users->links() }}
        </div>
    </div>

    @if ($lastGeneratedPassword)
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            <strong>Temporary password generated:</strong>
            <span class="font-mono">{{ $lastGeneratedPassword }}</span>
        </div>
    @endif

    <div
        x-cloak
        x-show="isOpen('user-form')"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4"
    >
        <div class="relative w-full max-w-2xl rounded-lg bg-white shadow-xl" x-transition.scale>
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-800">
                    {{ $editingUserId ? 'Edit User' : 'Add User' }}
                </h2>
                <button
                    type="button"
                    class="text-slate-400 hover:text-slate-600"
                    x-on:click="close('user-form')"
                >
                    ×
                </button>
            </div>

            <div class="p-6">
                <livewire:backoffice.admin.users.form
                    :user-id="$editingUserId"
                    :key="'user-form-' . $formInstance"
                />
            </div>
        </div>
    </div>
</div>
