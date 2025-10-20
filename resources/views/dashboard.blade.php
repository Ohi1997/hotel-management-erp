<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                <div class="p-8 text-center">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">
                        Welcome, {{ Auth::user()->name }} 👋
                    </h1>
                    <p class="text-gray-600 mb-8">
                        You’re logged in! Choose where you want to go next.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-xl mx-auto">
                        <!-- Backoffice Dashboard -->
                        <a href="{{ url('/backoffice/dashboard') }}"
                           class="group block bg-gradient-to-r from-indigo-500 to-blue-600 text-white p-6 rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-chart-line text-4xl mb-3 group-hover:scale-110 transition-transform"></i>
                                <h3 class="text-lg font-semibold">Backoffice Dashboard</h3>
                                <p class="text-sm text-indigo-100 mt-1">Manage bookings, customers, rooms, and more.</p>
                            </div>
                        </a>

                        <!-- Profile -->
                        <a href="{{ route('profile.edit') }}"
                           class="group block bg-gradient-to-r from-green-500 to-emerald-600 text-white p-6 rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-user-circle text-4xl mb-3 group-hover:scale-110 transition-transform"></i>
                                <h3 class="text-lg font-semibold">My Profile</h3>
                                <p class="text-sm text-emerald-100 mt-1">Edit your account information and settings.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
