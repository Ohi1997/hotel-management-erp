@props([
    'label' => '',
    'model' => '',
    'type' => 'text',
    'required' => false,
    'min' => null,
    'max' => null,
    'step' => null,
])

<label class="block text-xs font-semibold uppercase text-slate-500">
    {{ $label }}
    <input
        type="{{ $type }}"
        @if ($required) required @endif
        @if ($min !== null) min="{{ $min }}" @endif
        @if ($max !== null) max="{{ $max }}" @endif
        @if ($step !== null) step="{{ $step }}" @endif
        wire:model.live="{{ $model }}"
        class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
    >
</label>
@error($model)
    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
@enderror
