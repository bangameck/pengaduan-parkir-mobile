<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman form pengaturan.
     */
    public function index()
    {
        // Ambil semua setting dari database dan ubah menjadi format yang mudah diakses di view
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('super-admin.settings.index', compact('settings'));
    }

    /**
     * Menyimpan perubahan pengaturan ke database.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name'     => 'required|string|max:255',
            'fonnte_token' => 'nullable|string|max:255',
            'app_logo'     => 'nullable|image|mimes:png,jpg,jpeg|max:1024', // Logo maks 1MB
        ]);

        // Loop melalui data yang divalidasi dan simpan ke database
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Proses upload logo jika ada file baru
        if ($request->hasFile('app_logo')) {
            // Hapus logo lama jika ada
            $oldLogoPath = Setting::where('key', 'app_logo')->first()->value ?? null;
            if ($oldLogoPath) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            // Simpan logo baru
            $path = $request->file('app_logo')->store('settings', 'public');
            Setting::updateOrCreate(
                ['key' => 'app_logo'],
                ['value' => $path]
            );
        }

        Cache::forget('settings');

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
