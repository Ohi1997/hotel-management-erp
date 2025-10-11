<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Back Office</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{ display:none !important }</style>
</head>
<body class="bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md min-h-screen">
        <div class="p-4 font-bold text-lg border-b">Hotel ERP</div>
        <nav class="p-4 space-y-2">
            <a href="{{ route('backoffice.dashboard') }}" class="block p-2 rounded hover:bg-gray-200">
                Dashboard
            </a>

            @role('admin')
                <div class="text-gray-500 text-xs uppercase mt-4">Admin</div>
                <a href="#" class="block p-2 rounded hover:bg-gray-200">User Management</a>
                <a href="#" class="block p-2 rounded hover:bg-gray-200">System Settings</a>
            @endrole

            @role('manager')
                <div class="text-gray-500 text-xs uppercase mt-4">Manager</div>
                <a href="#" class="block p-2 rounded hover:bg-gray-200">Reports</a>
                <a href="#" class="block p-2 rounded hover:bg-gray-200">Inventory</a>
            @endrole

            @role('cashier')
                <div class="text-gray-500 text-xs uppercase mt-4">Cashier</div>
                <a href="#" class="block p-2 rounded hover:bg-gray-200">Customers</a>
                <a href="#" class="block p-2 rounded hover:bg-gray-200">Bookings</a>
                <a href="#" class="block p-2 rounded hover:bg-gray-200">Payments</a>
                <a href="#" class="block p-2 rounded hover:bg-gray-200">Wake-Up Calls</a>
            @endrole
        </nav>
    </aside>

    <!-- Main content area -->
    <main class="flex-1 p-6">
        {{-- 👇 Livewire components render here --}}
        {{ $slot }}
    </main>

    @vite(['resources/js/app.js'])
    @livewireScripts
</body>
</html>
