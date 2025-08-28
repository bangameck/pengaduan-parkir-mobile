<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Ambil data user yang sudah divalidasi
        $validatedData = $request->validated();
        $user          = $request->user();

        // Proses upload foto profil jika ada
        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $file     = $request->file('image');
            $filename = 'avatars/' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Kompres dan ubah ukuran gambar menjadi persegi (300x300)
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($file);
            $image->cover(300, 300); // <-- Crop & resize menjadi 300x300 px
            $image->toJpeg(80)->save(storage_path('app/public/' . $filename));

            // Simpan path gambar baru ke data yang akan di-update
            $validatedData['image'] = $filename;
        }

        // Jika user mengubah email, reset verifikasi email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Update data user
        $user->fill($validatedData);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
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
}
