<?php

use function Livewire\Volt\{state, mount};
use App\Models\Transaction;
state(['id']);

mount(function ($id) {
    $transaction = Transaction::find($id);
    $transaction->payment_status = 'paid';

    $transaction->save();
});

?>

<div>
    <h1>Pembayaran Berhasil</h1>
</div>
