<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemporaryFileController extends Controller
{
    /**
     * Menyimpan file sementara yang diunggah oleh FilePond.
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('=== TEMP UPLOAD DEBUG START ===');
        Log::info('Content-Type:', [$request->header('Content-Type')]);

        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        } elseif ($request->hasFile('filepond')) {
            $file = $request->file('filepond');
        }

        if (! $file || ! $file->isValid()) {
            Log::error('No valid file detected!');
            return response()->json(['error' => 'File tidak ditemukan atau tidak valid'], 422);
        }

        // --- PERUBAHAN DI SINI ---
        // Sesuaikan validasi ukuran file menjadi 20MB (20 * 1024 = 20480 KB)
        $request->validate([
            'file'     => 'file|mimes:jpeg,jpg,png,mp4,mov|max:20480',
            'filepond' => 'file|mimes:jpeg,jpg,png,mp4,mov|max:20480',
        ], [
            'file.mimes' => 'File harus gambar (jpg, png) atau video (mp4, mov).',
            'file.max'   => 'Ukuran file maksimal adalah 20MB.', // Perbarui pesan error
        ]);

        try {
            $extension = $file->getClientOriginalExtension();
            $filename  = Str::uuid() . '.' . $extension;
            $folder    = 'tmp/' . now()->format('YmdHis') . '-' . Str::random(10);

            Storage::disk('public')->makeDirectory($folder);
            $path = $file->storeAs($folder, $filename, 'public');

            Log::info('Temporary file uploaded successfully: ' . $path);

            return response()->json(['location' => $path]);

        } catch (\Exception $e) {
            Log::error('Upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengunggah file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus file sementara.
     */
    public function destroy(Request $request): JsonResponse
    {
        $filePath = trim($request->getContent());

        if (! $filePath || ! Storage::disk('public')->exists($filePath)) {
            Log::warning('Temporary file not found for deletion: ' . $filePath);
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        try {
            // Hapus file
            Storage::disk('public')->delete($filePath);

            // Hapus folder jika kosong
            $directory = dirname($filePath);
            if (empty(Storage::disk('public')->files($directory))) {
                Storage::disk('public')->deleteDirectory($directory);
                Log::info('Empty temp directory deleted: ' . $directory);
            }

            Log::info('Temporary file deleted successfully: ' . $filePath);
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Delete failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus file'], 500);
        }
    }
}
