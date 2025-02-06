<?php

use function Livewire\Volt\{layout, state};
use App\Models\Otp;
use App\Mail\RegistrationOtpMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\sendRegistrationOtp;

layout('components.layouts.auth');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
]);
//
$submit = function () {
    $credentials = $this->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8|confirmed',
    ]);
    // dd($credentials);

    $otp = Otp::create([
        'email' => $credentials['email'],
        'otp' => rand(100000, 999999),
        'expires_at' => now()->addMinutes(5),
    ]);
    Log::info('OTP created: ', ['otp' => $otp->otp]);

    $job = new sendRegistrationOtp($credentials['email'], $credentials['name'], $otp);
    dispatch($job);

    session()->flash('message', 'Registration successful. Please check your email for the OTP.');
    return redirect()->route('otp', ['credentials' => $credentials]);
};

?>

<div>
    <h2 class="mb-6 text-2xl font-bold text-center">Register</h2>

    <form wire:submit.prevent="submit" novalidate>
        @csrf
        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" id="name" name="name" wire:model="name"
                class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required placeholder="Enter your name">
            @error('name')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" wire:model="email"
                class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required placeholder="Enter your email">
            @error('email')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" wire:model="password"
                class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required placeholder="Enter your password">
            @error('password')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                wire:model="password_confirmation"
                class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required placeholder="Confirm your password">
        </div>
        <button type="submit" class="w-full px-4 py-2 mt-4 text-white bg-blue-500 rounded-md">Register</button>
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Already have an account? Login</a>
        </div>
    </form>
</div>
