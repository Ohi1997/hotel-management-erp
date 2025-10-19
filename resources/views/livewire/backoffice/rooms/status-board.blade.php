<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Room Status Board</h1>
            <p class="text-sm text-gray-500">Monitor current room availability and assignments.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ($statusOptions as $value => $label)
                @php
                    $isActive = ($status === null && $value === null) || ($status !== null && $status === $value);
                @endphp
                <button
                    type="button"
                    wire:click="setStatus({{ $value === null ? 'null' : '\'' . $value . '\'' }})"
                    class="rounded-full border px-4 py-2 text-sm font-medium transition {{ $isActive ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:text-gray-800' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        <select
            wire:model.live="floorId"
            class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 md:w-64"
        >
            <option value="">All floors</option>
            @foreach ($floors as $floor)
                <option value="{{ $floor->id }}">{{ $floor->name }} @if($floor->level !== null)(Level {{ $floor->level }})@endif</option>
            @endforeach
        </select>

        <select
            wire:model.live="roomTypeId"
            class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 md:w-64"
        >
            <option value="">All room types</option>
            @foreach ($roomTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($rooms as $room)
            @php
                $booking = $room->bookings->first();
                $badgeClasses = match ($room->status) {
                    'available' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'occupied' => 'bg-red-100 text-red-700 border-red-200',
                    'cleaning' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'maintenance' => 'bg-purple-100 text-purple-700 border-purple-200',
                    'out_of_service' => 'bg-gray-200 text-gray-700 border-gray-300',
                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                };
            @endphp

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Room {{ $room->number }}</h2>
                        <p class="text-sm text-gray-500">{{ $room->roomType?->name }} · Floor {{ $room->floor?->name }}</p>
                    </div>

                    <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase {{ $badgeClasses }}">
                        {{ str_replace('_', ' ', $room->status) }}
                    </span>
                </div>

                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <div>
                        <span class="font-semibold text-gray-800">Rate:</span>
                        ${{ number_format($room->rate, 2) }}
                    </div>

                    <div>
                        <span class="font-semibold text-gray-800">Smoking:</span>
                        {{ $room->is_smoking ? 'Allowed' : 'No' }}
                    </div>
                </div>

                @if ($booking)
                    <div class="mt-4 rounded border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                        <div class="font-semibold text-blue-900">Current guest</div>
                        <p>{{ $booking->customer->name }} ({{ $booking->customer->phone ?? '—' }})</p>
                        <p class="mt-1 text-xs">
                            {{ $booking->check_in_at?->format('M d, H:i') }} → {{ $booking->check_out_at?->format('M d, H:i') }}
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">
                No rooms found for the selected filters.
            </div>
        @endforelse
    </div>
</div>
