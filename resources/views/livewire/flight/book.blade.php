<?php

use function Livewire\Volt\{state};
use App\Models\Airport;

state([
    'departure' => 'DPS',
]);

$departures = Airport::all();

?>

<div class="max-w-4xl p-6 mx-auto bg-white rounded-lg shadow-md">
    <h2 class="mb-6 text-2xl font-bold text-center">Book a Flight</h2>

    <form action="{{ route('flight.search') }}" method="GET" class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="flex flex-col">
            <label for="departure" class="mb-2 font-medium text-gray-700">Departure</label>
            <select name="departure" id="departure"
                class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Departure</option>
                @foreach ($departures as $departure)
                    <option value="{{ $departure->id }}">{{ $departure->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col">
            <label for="arrival" class="mb-2 font-medium text-gray-700">Arrival</label>
            <select name="arrival" id="arrival"
                class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Arrival</option>
                @foreach ($departures as $departure)
                    <option value="{{ $departure->id }}">{{ $departure->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col">
            <label for="date" class="mb-2 font-medium text-gray-700">Date</label>
            <input type="date" name="date" id="date"
                class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex flex-col">
            <label for="qty" class="mb-2 font-medium text-gray-700">Quantity</label>
            <select name="qty" id="qty"
                class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="1">Select Quantity</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>
        <div class="flex flex-col md:col-span-2">
            <button type="submit"
                class="w-full p-3 text-white bg-blue-500 rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Search</button>
        </div>
    </form>
</div>
