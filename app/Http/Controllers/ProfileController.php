<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada
use Illuminate\Validation\Rule;
use Illuminate\View\View; // Pastikan ini ada
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

// Pastikan ini ada

class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Mengupdate informasi profil user.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $user          = $request->user();

        // Proses upload foto profil jika ada file baru yang dikirim
        if ($request->hasFile('image')) {
            // Hapus foto lama dari storage jika ada
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $file = $request->file('image');
            $path = 'avatars'; // Definisikan nama folder

            // === SOLUSI DITAMBAHKAN DI SINI ===
            // Cek jika direktori/folder 'avatars' belum ada di dalam 'storage/app/public'
            if (! Storage::disk('public')->exists($path)) {
                // Jika belum ada, maka buat folder tersebut
                Storage::disk('public')->makeDirectory($path);
            }
            // ===================================

            $filename = $user->username . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Kompres dan ubah ukuran gambar menjadi persegi (300x300)
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($file);
            $image->cover(300, 300); // Crop & resize menjadi 300x300 px
            $image->toJpeg(80)->save(storage_path('app/public/' . $path . '/' . $filename));

            // Simpan path gambar baru ke data yang akan di-update
            $validatedData['image'] = $path . '/' . $filename;
        }

        // Jika user mengubah email, reset verifikasi email
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Update data user
        $request->user()->fill($validatedData);
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroyImage(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->image) {
            Storage::disk('public')->delete($user->image);
            $user->image = null;
            $user->save();
        }

        return back()->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function validateField(Request $request): JsonResponse
    {
        $request->validate([
            'field' => ['required', Rule::in(['username', 'phone_number'])],
            'value' => ['required', 'string'],
        ]);

        $field  = $request->input('field');
        $value  = $request->input('value');
        $userId = Auth::id();

        // Cek apakah ada user LAIN yang sudah menggunakan value ini
        $exists = \App\Models\User::where($field, $value)
            ->where('id', '!=', $userId)
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
