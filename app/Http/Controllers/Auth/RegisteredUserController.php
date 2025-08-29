<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'regex:/^62[0-9]{9,15}$/', 'unique:users'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Siapkan data user, termasuk path gambar jika ada
        $userData             = $request->except(['password_confirmation', 'image']);
        $userData['password'] = Hash::make($request->password);
        $userData['role_id']  = Role::where('name', 'resident')->first()->id;

        // Proses upload foto profil jika ada file baru yang dikirim
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $path = 'avatars'; // Definisikan nama folder

            // === SOLUSI DITAMBAHKAN DI SINI ===
            // Cek jika direktori/folder 'avatars' belum ada di dalam 'storage/app/public'
            if (! Storage::disk('public')->exists($path)) {
                // Jika belum ada, maka buat folder tersebut
                Storage::disk('public')->makeDirectory($path);
            }
            // ===================================

            $filename = $request->username . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Kompres dan ubah ukuran gambar menjadi persegi (300x300)
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($file);
            $image->cover(300, 300); // Crop & resize menjadi 300x300 px
            $image->toJpeg(80)->save(storage_path('app/public/' . $path . '/' . $filename));

            // Simpan path gambar baru ke data yang akan di-update
            $userData['image'] = $path . '/' . $filename;
        }

        // Buat user dengan status is_active = false
        $user = User::create($userData);

        // Buat dan kirim OTP
        $otpCode              = rand(100000, 999999);
        $user->otp_code       = $otpCode;
        $user->otp_expires_at = now()->addMinutes(5); // OTP valid 5 menit
        $user->save();

        // Kirim WhatsApp via Fonnte
        try {
            Http::asForm()->withHeaders(['Authorization' => config('services.fonnte.token')])
                ->post('https://api.fonnte.com/send', [
                    'target'  => $user->phone_number,
                    'message' => "Halo {$user->name}, jangan berikan kode ini kepada siapapun! Kode verifikasi Anda adalah: *{$otpCode}*",
                ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim OTP via Fonnte: ' . $e->getMessage());
        }

        // Arahkan ke halaman verifikasi OTP
        return redirect()->route('otp.verification', ['user' => $user->username]);
    }
}
