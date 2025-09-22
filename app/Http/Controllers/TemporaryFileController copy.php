<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemporaryFileController extends Controller
{
    /**
     * Menyimpan file sementara yang diunggah oleh FilePond.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string Path ke file sementara yang disimpan.
     */
    public function store(Request $request)
    {
        // FilePond mengirim file dengan key 'filepond'
        if ($request->hasFile('filepond')) {
            $file     = $request->file('filepond');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Buat folder sementara yang unik untuk setiap sesi upload
            // Ini sangat penting untuk mencegah file dari user lain tercampur
            $folder = 'tmp/' . now()->timestamp . '-' . Str::random(10);

            // Simpan file dan kembalikan path lengkapnya
            $path = $file->storeAs($folder, $filename, 'public');

            // FilePond akan menggunakan path ini sebagai serverId
            return $path;
        }

        // Kembalikan string kosong jika tidak ada file
        return '';
    }

    /**
     * Menghapus file sementara saat pengguna membatalkan upload di FilePond.
     */
    public function destroy(Request $request)
    {
        // FilePond mengirim path file di dalam body request sebagai teks biasa
        $filePath = $request->getContent();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            // Hapus seluruh direktori sementara untuk membersihkan semuanya
            $directory = dirname($filePath);
            Storage::disk('public')->deleteDirectory($directory);

            // Kembalikan respons '204 No Content' yang menandakan sukses
            return response()->noContent();
        }

        return response()->json(['error' => 'File tidak ditemukan atau sudah dihapus.'], 404);
    }
}
