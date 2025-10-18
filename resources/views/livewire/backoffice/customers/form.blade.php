<form wire:submit.prevent="save" class="space-y-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-600">Full name <span class="text-rose-500">*</span></label>
            <input
                type="text"
                wire:model.defer="name"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
            @error('name')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Email</label>
            <input
                type="email"
                wire:model.defer="email"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
            @error('email')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Phone</label>
            <input
                type="text"
                wire:model.defer="phone"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
            @error('phone')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600">Government ID</label>
            <input
                type="text"
                wire:model.defer="gov_id"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
            @error('gov_id')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-600">Address</label>
            <input
                type="text"
                wire:model.defer="address"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
            @error('address')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-600">Notes</label>
            <textarea
                rows="3"
                wire:model.defer="notes"
                class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
            ></textarea>
            @error('notes')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-2">
        <button
            type="button"
            class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100"
            wire:click="$dispatch('close-modal')"
        >
            Cancel
        </button>
        <button
            type="submit"
            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            Save Customer
        </button>
    </div>
</form>
