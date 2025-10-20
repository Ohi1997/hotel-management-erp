@props([
    'title' => '',
    'description' => '',
])

<section {{ $attributes->merge(['class' => 'rounded-lg bg-white shadow-sm ring-1 ring-slate-200/70']) }}>
    <header class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-800">{{ $title }}</h2>
        @if ($description)
            <p class="text-xs text-slate-500">{{ $description }}</p>
        @endif
    </header>
    <div class="px-6 py-4">
        {{ $slot }}
    </div>
</section>
