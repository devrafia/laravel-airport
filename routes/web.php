<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::get('/', function () {
//     return view('welcome');
// });

Volt::route("/", "flight.book")->name('flight');
Volt::route("/flight", "flight.search")->name('flight.search');
Volt::route("/flight/{id}/choose-tier", "flight.tier")->name('flight.tier');
Volt::route("/flight/{id}/choose-seat", "flight.seat")->name('flight.seat');
Volt::route("/flight/pay", "flight.pay")->name('flight.pay');
Volt::route("/flight/history", "flight.history")->name('flight.history');

Volt::route("/checkout/success/{id}", "checkout.success")->name('checkout.success');
