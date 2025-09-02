<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordOtpController extends Controller
{
    // MENAMPILKAN FORM LUPA PASSWORD
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // MENGIRIM OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['phone_number' => 'required|string|exists:users,phone_number']);

        $user = User::where('phone_number', $request->phone_number)->first();

        // Buat OTP dan token sementara
        $otpCode = rand(100000, 999999);
        $token   = Str::random(60);

        // Simpan OTP & token ke user
        $user->update([
            'otp_code'       => $otpCode,
            'otp_expires_at' => now()->addMinutes(5),
            'remember_token' => $token, // Kita "pinjam" kolom ini untuk token reset
        ]);

        // Kirim WhatsApp via Fonnte
        try {
            Http::asForm()->withHeaders(['Authorization' => config('services.fonnte.token')])
                ->post('https://api.fonnte.com/send', [
                    'target'  => $user->phone_number,
                    'message' => "Kode OTP untuk reset password Anda adalah: *{$otpCode}*. Jangan berikan kepada siapapun.",
                ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim OTP Lupa Password: ' . $e->getMessage());
            return back()->withErrors(['phone_number' => 'Gagal mengirim OTP, silakan coba lagi nanti.']);
        }

        return redirect()->route('password.otp.verify', ['user' => $user->username])
            ->with('success', 'Kode OTP telah dikirim ke nomor WhatsApp Anda.');
    }

    // MENAMPILKAN FORM VERIFIKASI OTP
    public function showOtpForm(User $user)
    {
        return view('auth.verify-otp-password', ['user' => $user]);
    }

    // MEMPROSES VERIFIKASI OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp'      => 'required|numeric',
            'username' => 'required|string|exists:users,username',
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || $user->otp_code !== $request->otp || now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.']);
        }

        $token = $user->remember_token; // Ambil token yang tadi kita simpan

        // Hapus OTP setelah berhasil
        $user->update(['otp_code' => null, 'otp_expires_at' => null]);

        return redirect()->route('password.reset', ['user' => $user->username, 'token' => $token]);
    }

    // MENAMPILKAN FORM RESET PASSWORD BARU
    public function showResetForm(User $user, $token)
    {
        if ($user->remember_token !== $token) {
            return redirect()->route('password.request')->withErrors(['email' => 'Token reset password tidak valid.']);
        }
        return view('auth.reset-password', [
            'request' => request(),
            'user'    => $user,
            'token'   => $token, // Kirim token ke view
        ]);
    }

    // MENYIMPAN PASSWORD BARU (VERSI PERBAIKAN TOTAL)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'username' => ['required', 'string', 'exists:users,username'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || $user->remember_token !== $request->token) {
            return back()->withErrors(['username' => 'Token atau user tidak valid.']);
        }

        // Gunakan cara yang lebih sederhana dan pasti
        $user->password       = Hash::make($request->password);
        $user->remember_token = Str::random(60); // Ganti token setelah dipakai untuk keamanan
        $user->save();

        // Ganti 'status' menjadi 'success' agar bisa ditangkap oleh Swal2 di halaman login
        return redirect()->route('login')->with('success', 'Password Anda telah berhasil direset! Silakan login dengan password baru Anda.');
    }
}
