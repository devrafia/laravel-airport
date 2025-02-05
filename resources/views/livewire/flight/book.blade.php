<?php

use function Livewire\Volt\{state};
use App\Models\Airport;

state([
    'departure' => 'DPS',
]);

$departures = Airport::all();

?>

<div class="border-2 border-black">

    <form action="{{ route('flight.search') }}" class="flex flex-row items-center justify-center gap-10">
        <div class="flex flex-col">
            <label for="departure">Departure</label>
            <select name="departure" id="departure">
                <option value="">Select Departure</option>
                @foreach ($departures as $departure)
                    <option value="{{ $departure->id }}">{{ $departure->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col">
            <label for="arrival">Arrival</label>
            <select name="arrival" id="arrival">
                <option value="">Select Departure</option>
                @foreach ($departures as $departure)
                    <option value="{{ $departure->id }}">{{ $departure->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col">
            <label for="Date">Date</label>
            <input type="date" name="date">
        </div>
        <div class="flex flex-col">
            <label for="qty">Quantity</label>
            <select name="qty" id="qty">
                <option value="1">Select Quantity</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>
        <div class="flex flex-col">
            <button type="submit">search</button>
        </div>
    </form>
</div>
