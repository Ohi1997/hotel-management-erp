<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Room status board</h1>
            <p class="text-sm text-slate-500">Monitor occupancy, cleanliness, and assignments.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <select wire:model.live="statusFilter" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="all">All statuses</option>
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Maintenance</option>
                <option value="out_of_service">Out of service</option>
            </select>
            <select wire:model.live="floorFilter" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="">All floors</option>
                @foreach ($floors as $floor)
                    <option value="{{ $floor->id }}">{{ $floor->name }} {{ $floor->level ? '(Level '.$floor->level.')' : '' }}</option>
                @endforeach
            </select>
            <select wire:model.live="typeFilter" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="">All types</option>
                @foreach ($roomTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($rooms as $room)
            <div wire:key="room-card-{{ $room->id }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Room {{ $room->number }}</h2>
                        <p class="text-xs uppercase tracking-wide text-slate-500">{{ strtoupper($room->status) }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $room->is_clean ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                        {{ $room->is_clean ? 'Clean' : 'Needs service' }}
                    </span>
                </div>
                <div class="mt-3 space-y-1 text-xs text-slate-500">
                    <div>Type: {{ $room->roomType?->name }}</div>
                    <div>Floor: {{ $room->floor?->name ?? 'N/A' }}</div>
                    @php($latestBooking = $room->bookings->first())
                    @if ($latestBooking)
                        <div>Guest: {{ $latestBooking->customer?->name }}</div>
                        <div>Check-in: {{ optional($latestBooking->check_in_at)->format('M d, Y H:i') }}</div>
                    @else
                        <div>No active booking</div>
                    @endif
                </div>
                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    <button type="button" wire:click="updateStatus({{ $room->id }}, 'available')" class="rounded-md border border-slate-200 px-3 py-1 text-slate-600 hover:bg-slate-100">Mark available</button>
                    <button type="button" wire:click="updateStatus({{ $room->id }}, 'maintenance')" class="rounded-md border border-slate-200 px-3 py-1 text-slate-600 hover:bg-slate-100">Maintenance</button>
                    <button type="button" wire:click="toggleClean({{ $room->id }})" class="rounded-md border border-slate-200 px-3 py-1 text-slate-600 hover:bg-slate-100">Toggle clean</button>
                </div>
            </div>
        @empty
            <p class="col-span-full rounded-lg border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">No rooms configured yet.</p>
        @endforelse
    </div>
</div>
