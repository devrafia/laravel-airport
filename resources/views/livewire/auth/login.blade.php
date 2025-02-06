<?php

use function Livewire\Volt\{layout, state};
layout('components.layouts.auth');
//
?>
<div>
    <h2 class="mb-6 text-2xl font-bold text-center">Login</h2>
    <form action="{{ route('authenticate') }}" method="POST" novalidate>
        @csrf
        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email"
                class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required placeholder="Enter your email">
            @error('email')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password"
                class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required placeholder="Enter your password">
            @error('password')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Login</button>
    </form>

    <div class="mt-4 text-center">
        <p>Don't have an account? <a href="{{ route('register') }}" class="text-blue-500">Register here</a></p>
    </div>
</div>
