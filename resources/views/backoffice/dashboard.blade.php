@extends('layouts.backoffice')

@section('page-title', 'Dashboard')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500">Overview of current hotel metrics.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-600">Occupancy Today</h2>
            <p class="mt-2 text-2xl font-bold text-slate-900">72%</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-600">Arrivals / Departures</h2>
            <p class="mt-2 text-2xl font-bold text-slate-900">24 / 18</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-600">Revenue (Daily)</h2>
            <p class="mt-2 text-2xl font-bold text-slate-900">$18,450</p>
        </div>
    </div>
@endsection
