<div class="bg-white rounded-lg shadow p-6 w-full max-w-lg mx-auto">
    <h2 class="text-xl font-semibold mb-4">
        {{ $customerId ? 'Edit Customer' : 'Add Customer' }}
    </h2>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Name *</label>
            <input type="text" wire:model.defer="form.name" class="w-full border rounded p-2">
            @error('form.name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" wire:model.defer="form.email" class="w-full border rounded p-2">
                @error('form.email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input type="text" wire:model.defer="form.phone" class="w-full border rounded p-2">
                @error('form.phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Gov ID</label>
                <input type="text" wire:model.defer="form.gov_id" class="w-full border rounded p-2">
                @error('form.gov_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Address</label>
                <input type="text" wire:model.defer="form.address" class="w-full border rounded p-2">
                @error('form.address') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Notes</label>
            <textarea wire:model.defer="form.notes" class="w-full border rounded p-2" rows="2"></textarea>
            @error('form.notes') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <button
                type="button"
                class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
                wire:click="$dispatch('modal-close', { id: 'customer-form' })"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-75"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Save</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </form>
</div>
