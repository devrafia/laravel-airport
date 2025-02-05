<?php

namespace App\Http\Controllers;

use App\Mail\SuccessPaymentMail;
use App\Models\FlightClass;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Midtrans\Config;

class PaymentController extends Controller
{
    public function createTransaction(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);

        $transaction = Transaction::create([
            'flight_id' => $request->flight_id,
            'flight_class_id' => $request->selectedClassId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'number_of_passengers' => 1,
            'payment_status' => 'pending',
            'subtotal' => $request->price,
            'grandtotal' => $request->price,
        ]);

        $flightClass = FlightClass::find($request->selectedClassId);

        // Set your Merchant Server Key
        Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = false;
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->code, // ID unik untuk transaksi
                'gross_amount' => 1000, // Total pembayaran
            ],
            'customer_details' => [
                'first_name' => $transaction->name,
                'email' => $transaction->email,
                'phone' => $transaction->phone,
            ],
            'item_details' => [
                [
                    'id' => 'flight-' . $request->flight_id,
                    'name' => 'Flight Ticket - ' . $flightClass->class_type, // Nama kelas penerbangan
                    'quantity' => 1,
                    'price' => $request->price, // Harga tiket
                ],
            ],
        ];
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $transaction->snap_token = $snapToken;
        $transaction->save();

        return response()->json(['snap_token' => $snapToken]);
    }

    public function sendSuccessEmail(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Kirim email ke pengguna
        Mail::to($transaction->email)->send(new SuccessPaymentMail($transaction));

        return response()->json(['message' => 'Email sent successfully']);
    }

    public function handleWebhook(Request $request)
    {
        $serverKey = config('midtrans.serverKey');
        $signatureKey = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($signatureKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transaction = Transaction::where('code', $request->order_id)->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found.',
            ], 404);
        }

        if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
            $transaction->payment_status = 'paid';
        } elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'expire') {
            $transaction->payment_status = 'failed';
        } elseif ($request->transaction_status == 'pending') {
            $transaction->payment_status = 'pending';
        }
        // $transaction->payment_status = 'paid';
        $transaction->save();

        return response()->json(['message' => 'Webhook processed successfully']);
    }
}
