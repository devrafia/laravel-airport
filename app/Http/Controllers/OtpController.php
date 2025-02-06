<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationOtpMail;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function generateOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Hapus OTP lama jika ada
        Otp::where('email', $request->email)->delete();

        // Generate OTP 6 digit
        $otpCode = rand(100000, 999999);

        // Simpan ke database dengan masa berlaku 5 menit
        $otp = Otp::create([
            'email' => $request->email,
            'otp' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // $transaction = Transaction::findOrFail($id);
        $email = $request->email;

        // Kirim email ke pengguna
        Mail::to($otp->email)->send(new RegistrationOtpMail($otp->otp, $email));

        return response()->json(['message' => 'OTP sent to your email']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        // Hapus OTP setelah berhasil digunakan
        $otp->delete();

        return response()->json(['message' => 'OTP verified successfully']);
    }
}
