<div class="bg-white rounded-lg shadow p-6 w-full max-w-lg mx-auto">
    <h2 class="text-xl font-semibold mb-4">
        {{ $customer_id ? 'Edit Customer' : 'Add Customer' }}
    </h2>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Name *</label>
            <input type="text" wire:model.defer="name" class="w-full border rounded p-2">
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" wire:model.defer="email" class="w-full border rounded p-2">
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input type="text" wire:model.defer="phone" class="w-full border rounded p-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Gov ID</label>
            <input type="text" wire:model.defer="gov_id" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Address</label>
            <input type="text" wire:model.defer="address" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Notes</label>
            <textarea wire:model.defer="notes" class="w-full border rounded p-2" rows="2"></textarea>
        </div>

        <div class="flex justify-end space-x-2">
            <button type="button" wire:click="$dispatch('closeModal')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
        </div>
    </form>
</div>
