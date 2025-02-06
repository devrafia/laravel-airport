<?php

use App\Http\Controllers\AuthConroller;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::middleware(['guest'])->group(function () {
    Volt::route("/login", "auth.login")->name('login')->methods(['GET', 'POST']);
    Volt::route("/register", "auth.registration")->name('register');
    Route::post('/authenticate', [AuthConroller::class, 'authenticate'])->name('authenticate');
    Volt::route("/otp", "auth.otp")->name('otp');
});

Route::middleware(['auth'])->group(function () {
    Volt::route("/", "flight.book")->name('flight');
    Volt::route("/flight", "flight.search")->name('flight.search');
    Volt::route("/flight/{id}/choose-tier", "flight.tier")->name('flight.tier');
    Volt::route("/flight/{id}/choose-seat", "flight.seat")->name('flight.seat');
    Volt::route("/flight/pay", "flight.pay")->name('flight.pay');
    Volt::route("/flight/history", "flight.history")->name('flight.history');

    Route::get('/logout', [AuthConroller::class, 'logout'])->name('logout');
});

Volt::route("/checkout/success/{id}", "checkout.success")->name('checkout.success');
