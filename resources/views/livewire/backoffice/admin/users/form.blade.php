<form wire:submit.prevent="save" class="space-y-6">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-xs font-semibold uppercase text-slate-500">Name</label>
            <input
                type="text"
                wire:model.live="form.name"
                class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
            @error('form.name')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-500">Email</label>
            <input
                type="email"
                wire:model.live="form.email"
                class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
            @error('form.email')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-xs font-semibold uppercase text-slate-500">Password</label>
            <input
                type="password"
                wire:model="form.password"
                placeholder="{{ $userId ? 'Leave blank to keep current password' : '' }}"
                class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
            @error('form.password')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-500">Confirm Password</label>
            <input
                type="password"
                wire:model="form.password_confirmation"
                class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
            @error('form.password_confirmation')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-xs font-semibold uppercase text-slate-500">Role</label>
            <select
                wire:model.live="form.role"
                class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
                @foreach ($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            @error('form.role')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-500">Assign Hotel</label>
            <select
                wire:model.live="form.hotel_id"
                class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
                <option value="">No hotel assignment</option>
                @foreach ($hotels as $hotelId => $hotelName)
                    <option value="{{ $hotelId }}">{{ $hotelName }}</option>
                @endforeach
            </select>
            @error('form.hotel_id')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center justify-between">
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" wire:model="form.status" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            Active user
        </label>
        @error('form.status')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end gap-3">
        <button
            type="button"
            class="rounded border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
            wire:click="$dispatch('modal-close', { id: 'user-form' })"
        >
            Cancel
        </button>
        <button
            type="submit"
            class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
        >
            {{ $userId ? 'Update User' : 'Create User' }}
        </button>
    </div>
</form>
