<?php

use App\Mail\SuccessPaymentMail;
use App\Mail\TaskSchedulerMail;
use App\Models\Otp;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    Otp::where('expires_at', '<', Carbon::now())->delete();
})->everyFiveMinutes();
