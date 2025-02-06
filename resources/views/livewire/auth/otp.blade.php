<?php

use function Livewire\Volt\{layout, state, mount};
use App\Models\Otp;
use Carbon\Carbon;
use App\Models\User;

layout('components.layouts.auth');
state(['credentials', 'otp' => []]);

mount(function () {
    $this->credentials = request()->credentials;
});

$verify = function () {
    $this->validate([
        'otp' => 'required|array|size:6',
    ]);

    $otpCode = implode('', $this->otp);
    Log::info("OTP code: $otpCode");
    Log::info("Email: {$this->credentials['email']}");
    $otp = Otp::where('email', $this->credentials['email'])->where('otp', $otpCode)->latest()->first();
    if (!$otp) {
        session()->flash('error', 'Invalid OTP');
        return;
    }

    if ($otp->expires_at < Carbon::now()) {
        session()->flash('error', 'OTP expired');
        return;
    }

    // Hapus OTP setelah berhasil digunakan
    $otp->delete();
    User::create($this->credentials);

    return to_route('login');
};
?>

<div>
    <h2 class="mb-6 text-2xl font-bold text-center">Enter OTP</h2>

    @if (session('error'))
        <div class="mb-4">
            <div class="text-red-600">
                <ul>
                    <li>{{ session('error') }}</li>
                </ul>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4">
            <div class="text-red-600">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="verify" novalidate>
        @csrf
        <div class="flex justify-center mb-4">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" id="otp{{ $i }}" wire:model="otp.{{ $i }}" maxlength="1"
                    class="w-12 h-12 mx-1 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required oninput="moveFocus(event, {{ $i }})">
            @endfor
        </div>
        <button type="submit" class="w-full px-4 py-2 mt-4 text-white bg-blue-500 rounded-md">Verify OTP</button>
    </form>
</div>

<script>
    function moveFocus(event, index) {
        const input = event.target;
        if (input.value.length === 1 && index <= 5) {
            document.getElementById(`otp${index + 1}`).focus();
        } else {
            document.getElementById(`otp${index - 1}`).focus();
        }
    }
</script>
