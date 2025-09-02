<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpVerificationController extends Controller
{
    public function show(User $user)
    {
        return view('auth.otp-verification', ['user' => $user]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp'      => 'required|numeric',
            'username' => 'required|string|exists:users,username',
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || $user->is_active) {
            return back()->withErrors(['otp' => 'Verifikasi gagal.']);
        }

        if ($user->otp_code !== $request->otp || now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.']);
        }

        // OTP Benar, aktifkan user
        $user->is_active      = true;
        $user->otp_code       = null;
        $user->otp_expires_at = null;
        $user->save();

        // Login user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Akun Anda berhasil diaktifkan!');
    }

    public function resend(User $user)
    {
        // Pastikan user valid dan belum aktif
        if (! $user || $user->is_active) {
            return redirect()->route('login')->withErrors(['otp' => 'Akun tidak valid.']);
        }

        // Buat dan kirim OTP baru
        $otpCode              = rand(100000, 999999);
        $user->otp_code       = $otpCode;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        // Kirim WhatsApp via Fonnte
        try {
            Http::asForm()->withHeaders(['Authorization' => config('services.fonnte.token')])
                ->post('https://api.fonnte.com/send', [
                    'target'  => $user->phone_number,
                    'message' => "Ini adalah kode verifikasi BARU Anda: *{$otpCode}*. Jangan berikan kepada siapapun.",
                ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim ulang OTP via Fonnte: ' . $e->getMessage());
        }

        // Kembalikan ke halaman verifikasi dengan pesan sukses
        return back()->with('success', 'Kode OTP baru telah berhasil dikirim ke nomor WhatsApp Anda.');
    }
}
