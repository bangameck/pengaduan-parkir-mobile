<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // --- MULAI MODIFIKASI ---

        // 1. Cari role 'resident' di database
        $residentRole = Role::where('name', 'resident')->first();

        // Opsi pengaman: jika role 'resident' tidak ditemukan di database
        if (! $residentRole) {
            // Log error atau handle sesuai kebutuhan
            Log::error('Default role "resident" not found during registration.');
            // Mungkin redirect kembali dengan pesan error
            return back()->withErrors(['msg' => 'Registration is currently unavailable.']);
        }

        // 2. Buat user dengan menambahkan role_id
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $residentRole->id, // <-- Baris penting ditambahkan di sini
        ]);

        // --- SELESAI MODIFIKASI ---

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
