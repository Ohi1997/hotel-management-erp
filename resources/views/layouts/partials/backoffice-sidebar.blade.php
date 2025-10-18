<div class="flex h-full flex-col">
    <div class="flex items-center gap-2 px-6 py-5 text-lg font-semibold text-slate-800">
        <span class="rounded-lg bg-indigo-600 px-2 py-1 text-sm font-bold text-white">ERP</span>
        <span>{{ config('app.name', 'Hotel ERP') }}</span>
    </div>
    <nav class="flex-1 space-y-1 px-4">
        @foreach ($navigation as $item)
            @php
                $hasAccess = $user?->hasAnyRole($item['roles']) ?? true;
                $isActive = request()->routeIs($item['route']);
            @endphp
            @if ($hasAccess)
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium {{ $isActive ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    <span>{{ ucfirst($item['icon']) }}</span>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </nav>
</div>
