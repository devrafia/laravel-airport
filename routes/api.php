<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/create-transaction', [PaymentController::class, 'createTransaction'])->name('checkout.create');
Route::post('/checkout/success/{id}', [PaymentController::class, 'sendSuccessEmail'])->name('checkout.mail');

Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');
