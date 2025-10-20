<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hotel Backoffice') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-72 border-r border-slate-200 bg-white lg:block">
            <div class="border-b border-slate-200 px-6 py-4">
                <span class="text-lg font-semibold text-slate-800">Hotel Management</span>
                <p class="text-xs text-slate-500">Backoffice Console</p>
            </div>

            <nav class="space-y-6 px-4 py-6 text-sm">
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">General</p>
                    <x-backoffice.nav-link
                        icon="home"
                        :href="route('backoffice.dashboard')"
                        :active="request()->routeIs('backoffice.dashboard')"
                    >
                        Dashboard
                    </x-backoffice.nav-link>
                </div>

                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Operations</p>
                    @can('viewAny', App\Models\Customer::class)
                        <x-backoffice.nav-link
                            icon="users"
                            :href="route('backoffice.customers.index')"
                            :active="request()->routeIs('backoffice.customers.*')"
                        >
                            Customers
                        </x-backoffice.nav-link>
                    @endcan

                    @can('viewAny', App\Models\Booking::class)
                        <x-backoffice.nav-link
                            icon="calendar"
                            :href="route('backoffice.bookings.index')"
                            :active="request()->routeIs('backoffice.bookings.*')"
                        >
                            Bookings
                        </x-backoffice.nav-link>
                    @endcan

                    @can('viewAny', App\Models\Payment::class)
                        <x-backoffice.nav-link
                            icon="credit-card"
                            :href="route('backoffice.payments.index')"
                            :active="request()->routeIs('backoffice.payments.*')"
                        >
                            Payments
                        </x-backoffice.nav-link>
                    @endcan

                    @can('viewAny', App\Models\WakeUp::class)
                        <x-backoffice.nav-link
                            icon="bell"
                            :href="route('backoffice.wakeups.index')"
                            :active="request()->routeIs('backoffice.wakeups.*')"
                        >
                            Wake-Up Calls
                        </x-backoffice.nav-link>
                    @endcan
                </div>

                @role('admin|manager')
                    <div class="space-y-1">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Rooms</p>

                        <x-backoffice.nav-link
                            icon="building"
                            :href="route('backoffice.rooms.board')"
                            :active="request()->routeIs('backoffice.rooms.board')"
                        >
                            Status Board
                        </x-backoffice.nav-link>

                        <x-backoffice.nav-link
                            icon="layers"
                            :href="route('backoffice.rooms.types')"
                            :active="request()->routeIs('backoffice.rooms.types')"
                        >
                            Room Types
                        </x-backoffice.nav-link>

                        <x-backoffice.nav-link
                            icon="map"
                            :href="route('backoffice.rooms.floors')"
                            :active="request()->routeIs('backoffice.rooms.floors')"
                        >
                            Floors
                        </x-backoffice.nav-link>
                    </div>
                @endrole

                @role('admin')
                    <div class="space-y-1">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Administration</p>

                        <x-backoffice.nav-link
                            icon="users"
                            :href="route('backoffice.admin.users')"
                            :active="request()->routeIs('backoffice.admin.users')"
                        >
                            Users
                        </x-backoffice.nav-link>

                        <x-backoffice.nav-link
                            icon="key"
                            :href="route('backoffice.admin.roles')"
                            :active="request()->routeIs('backoffice.admin.roles')"
                        >
                            Roles &amp; Permissions
                        </x-backoffice.nav-link>

                        <x-backoffice.nav-link
                            icon="shield"
                            :href="route('backoffice.admin.settings')"
                            :active="request()->routeIs('backoffice.admin.settings')"
                        >
                            System Settings
                        </x-backoffice.nav-link>
                    </div>
                @endrole
            </nav>
        </aside>

        <div class="flex w-full flex-col">
            <header class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                <div class="flex items-center gap-3">
                    @php($currentTitle = $pageTitle ?? trim($__env->yieldContent('page-title')))
                    <h1 class="text-lg font-semibold text-slate-800">
                        {{ filled($currentTitle) ? $currentTitle : 'Backoffice' }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-500">{{ auth()->user()?->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-md border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-8">
                @if (trim($__env->yieldContent('content')))
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    <div
        x-data="toastStack()"
        x-on:toast.window="push($event.detail)"
        class="pointer-events-none fixed inset-x-0 top-4 z-50 flex flex-col items-center space-y-3"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition.opacity.duration.200ms
                class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-lg"
                :class="toast.typeClasses"
            >
                <div class="text-sm font-medium" x-text="toast.message"></div>
                <button
                    type="button"
                    class="ml-auto text-slate-400 hover:text-slate-600"
                    x-on:click="dismiss(toast.id)"
                >
                    &times;
                </button>
            </div>
        </template>
    </div>

    @livewireScripts

    <script>
        function toastStack() {
            return {
                toasts: [],
                push(detail = {}) {
                    const id = self.crypto?.randomUUID ? crypto.randomUUID() : Date.now().toString();
                    const type = detail.type || 'info';
                    const typeClasses = {
                        success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
                        error: 'border-rose-200 bg-rose-50 text-rose-800',
                        deleted: 'border-slate-200 bg-slate-50 text-slate-700',
                        info: 'border-sky-200 bg-sky-50 text-sky-800',
                    }[type] || 'border-slate-200 bg-slate-50 text-slate-700';

                    const toast = {
                        id,
                        message: detail.message || 'Action completed.',
                        typeClasses,
                        visible: true,
                    };

                    this.toasts.push(toast);

                    setTimeout(() => this.dismiss(id), detail.timeout || 3500);
                },
                dismiss(id) {
                    const toast = this.toasts.find((item) => item.id === id);
                    if (!toast) return;

                    toast.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter((item) => item.id !== id);
                    }, 200);
                },
            };
        }
    </script>
</body>
</html>

