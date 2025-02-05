<?php

use App\Mail\SuccessPaymentMail;
use App\Mail\TaskSchedulerMail;
use App\Models\Transaction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $transaction = Transaction::findOrFail(78);

    // Kirim email ke pengguna
    Mail::to($transaction->email)->send(new TaskSchedulerMail($transaction));
})->everyThirtySeconds();
