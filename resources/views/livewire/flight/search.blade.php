<?php

use function Livewire\Volt\{state, mount};
use Carbon\Carbon;
use App\Models\Flight;
use App\Models\FlightSegment;
use App\Models\Airport;

state([
    'departures' => [],
    'segments' => [],
]);
mount(function () {
    $this->segments = FlightSegment::query()->where('airport_id', request()->departure)->where('sequence', 1)->where('time', '>=', Carbon::today())->get();
});

$arrival = FlightSegment::query()->where('airport_id', request()->arrival)->where('sequence', 2)->get();

?>

<div class="border-2 border-black">
    <div class="flex flex-row gap-4 p-4 border-2 rounded-lg w-max">
        <div>
            <h3>Departure</h3>
            <h4 class="font-bold">DPS</h4>
        </div>
        <div>
            <h3>Arrival</h3>
            <h4 class="font-bold">-</h4>
        </div>
        <div>
            <h3>Date</h3>
            <h4 class="font-bold">2024-11-16</h4>
        </div>
        <div>
            <h3>Quantity</h3>
            <h4 class="font-bold">1 People</h4>
        </div>
    </div>
    <div class="mt-4">
        <h1 class="font-bold">Available Flights</h1>
        <div class="flex flex-col gap-2">
            @foreach ($segments as $segment)
                <div class="flex flex-row items-center gap-4 p-2 border border-black w-max">
                    <div class="airline-logo">
                        <img src="{{ asset('storage/' . $segment->flight->airline->logo) }}"
                            alt="{{ $segment->flight->airline->name }} Logo" class="object-contain w-20 h-20">
                    </div>
                    <div class="flex flex-col">
                        <h1>{{ $segment->flight->airline->name }}</h1>
                        <span>{{ Carbon::parse($segment->time)->format('H:i') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span>5 hours</span>
                        <span>DPS - JED</span>
                    </div>
                    <div class="flex flex-col">
                        <a href="{{ route('flight.tier', ['id' => $segment->flight->id]) }}">Choose</a>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
