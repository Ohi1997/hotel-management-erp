@extends('layouts.backoffice')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-4 rounded shadow">Occupancy Today</div>
        <div class="bg-white p-4 rounded shadow">Arrivals/Departures</div>
        <div class="bg-white p-4 rounded shadow">Revenue</div>
    </div>
@endsection
