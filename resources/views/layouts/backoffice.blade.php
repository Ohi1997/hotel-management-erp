<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Backoffice') }}</title>
        <style>[x-cloak]{display:none!important}</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full bg-slate-100 antialiased">
        @php
            $navigation = [
                ['label' => 'Dashboard', 'route' => 'backoffice.dashboard', 'icon' => 'home', 'roles' => ['admin', 'manager', 'cashier']],
                ['label' => 'Customers', 'route' => 'backoffice.customers.index', 'icon' => 'users', 'roles' => ['admin', 'manager', 'cashier']],
                ['label' => 'Bookings', 'route' => 'backoffice.bookings.index', 'icon' => 'calendar', 'roles' => ['admin', 'manager']],
                ['label' => 'Payments', 'route' => 'backoffice.payments.index', 'icon' => 'credit-card', 'roles' => ['admin', 'cashier']],
                ['label' => 'Rooms board', 'route' => 'backoffice.rooms.board', 'icon' => 'building-library', 'roles' => ['admin', 'manager']],
                ['label' => 'Room types', 'route' => 'backoffice.rooms.types', 'icon' => 'adjustments-horizontal', 'roles' => ['admin']],
                ['label' => 'Floors', 'route' => 'backoffice.rooms.floors', 'icon' => 'building-office', 'roles' => ['admin']],
                ['label' => 'Wake-ups', 'route' => 'backoffice.wake-ups.index', 'icon' => 'bell', 'roles' => ['admin', 'manager', 'cashier']],
            ];
            $user = auth()->user();
        @endphp

        <div class="min-h-full" x-data="{ sidebarOpen: false }">
            <div class="lg:hidden">
                <div class="fixed inset-0 z-40 bg-slate-900/50" x-show="sidebarOpen" x-cloak x-transition.opacity></div>
                <div class="fixed inset-y-0 left-0 z-50 w-64 transform bg-white shadow-xl" x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                    @include('layouts.partials.backoffice-sidebar', ['navigation' => $navigation, 'user' => $user])
                </div>
            </div>

            <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
                <div class="flex grow flex-col bg-white shadow-lg">
                    @include('layouts.partials.backoffice-sidebar', ['navigation' => $navigation, 'user' => $user])
                </div>
            </div>

            <div class="lg:pl-64">
                <header class="sticky top-0 z-30 bg-white shadow-sm">
                    <div class="flex items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button type="button" class="inline-flex items-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = !sidebarOpen">
                                ☰
                            </button>
                            <span class="text-sm font-semibold text-slate-500">{{ now()->format('l, M j Y') }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-slate-600">
                            <span class="hidden sm:inline">{{ $user?->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-md border border-slate-200 px-3 py-1 text-sm text-slate-600 hover:bg-slate-100">Logout</button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>
            </div>
        </div>

        <div
            x-data="toastStack()"
            class="pointer-events-none fixed inset-x-0 top-4 z-50 flex flex-col items-center space-y-3"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    x-show="toast.visible"
                    x-transition.opacity.duration.200ms
                    class="pointer-events-auto flex max-w-sm items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-lg"
                >
                    <div :class="toast.color" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white">
                        <span x-text="toast.icon"></span>
                    </div>
                    <div class="text-sm text-slate-700" x-text="toast.message"></div>
                </div>
            </template>
        </div>

        @livewireScripts
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('toastStack', () => ({
                    toasts: [],
                    init() {
                        window.addEventListener('toast-notify', (event) => {
                            const { message = '', type = 'info' } = event.detail ?? {};
                            this.pushToast(message, type);
                        });
                    },
                    pushToast(message, type) {
                        const palette = {
                            success: { color: 'bg-emerald-500', icon: '✓' },
                            deleted: { color: 'bg-rose-500', icon: '🗑' },
                            error: { color: 'bg-rose-500', icon: '!' },
                            info: { color: 'bg-slate-500', icon: 'ℹ' },
                        };
                        const id = window.crypto?.randomUUID ? window.crypto.randomUUID() : Math.random().toString(36).slice(2, 10);
                        const meta = palette[type] ?? palette.info;
                        this.toasts.push({ id, message, visible: true, ...meta });
                        setTimeout(() => this.dismiss(id), 3500);
                    },
                    dismiss(id) {
                        const toast = this.toasts.find(t => t.id === id);
                        if (!toast) return;
                        toast.visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300);
                    },
                }));
            });
        </script>
    </body>
</html>
