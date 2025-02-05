<!DOCTYPE html>
<html>

<head>
    <title>Payment Success</title>
</head>

<body>
    <h1>Thank you for your purchase!</h1>
    <p>Hi {{ $transaction->name }},</p>
    <p>Your payment has been successfully processed.</p>
    <p>Transaction Code: {{ $transaction->code }}</p>
    <p>Amount: Rp {{ number_format($transaction->grandtotal, 2) }}</p>
    <p>Thank you for purchasing your ticket with us!</p>
</body>

</html>
