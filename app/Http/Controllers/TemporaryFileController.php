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
        // PERBAIKAN: Log detail FULL request di AWAL (sebelum apapun)
        Log::info('=== TEMP UPLOAD DEBUG START ===');
        Log::info('Content-Type:', [$request->header('Content-Type')]);
        Log::info('All request data:', $request->all());
        Log::info('All files in request:', $request->allFiles());
        Log::info('Has specific fields?', [
            'has_file'     => $request->hasFile('file'),
            'has_filepond' => $request->hasFile('filepond'),
            'has_File'     => $request->hasFile('File'),
            'files_array'  => $request->hasFile('file') ? count($request->file('file')) : 0, // Jika array
        ]);

        // PERBAIKAN: Flexible detect file (cek multiple nama field, termasuk array untuk multiple upload)
        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file'); // Single atau array
            if (is_array($file)) {
                $file = $file[0];
            }
            // Ambil pertama jika array
        } elseif ($request->hasFile('filepond')) {
            $file = $request->file('filepond');
            if (is_array($file)) {
                $file = $file[0];
            }

        } elseif ($request->hasFile('File')) {
            $file = $request->file('File');
            if (is_array($file)) {
                $file = $file[0];
            }

        }

        if (! $file || ! $file->isValid()) {
            Log::error('No valid file detected! Available files: ' . json_encode($request->allFiles()));
            return response()->json(['error' => 'File tidak ditemukan atau tidak valid'], 422);
        }

        // PERBAIKAN: Validate file setelah detect (mimes + size)
        $request->validate([
            'file'     => 'file|mimes:jpeg,jpg,png,mp4,mov|max:10240', // Max 10MB, fallback ke 'file' untuk validate
            'filepond' => 'file|mimes:jpeg,jpg,png,mp4,mov|max:10240', // Backup jika field lain
        ], [
            'file.mimes' => 'File harus gambar (jpg, png) atau video (mp4, mov).',
            'file.max'   => 'File maksimal 10MB.',
        ]);

        try {
            $extension    = $file->getClientOriginalExtension();
            $filename     = Str::uuid() . '.' . $extension;
            $timestamp    = now()->format('YmdHis');
            $randomString = Str::random(10);
            $folder       = 'tmp/' . $timestamp . '-' . $randomString;

            // Buat folder recursive jika belum ada
            Storage::disk('public')->makeDirectory($folder, 0755, true);

            // Simpan file
            $path = $file->storeAs($folder, $filename, 'public');

            Log::info('Temporary file uploaded successfully: ' . $path, [
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
                'mime'          => $file->getMimeType(),
            ]);

            // PERBAIKAN: Return JSON {location: path} untuk FilePond
            return response()->json([
                'location' => $path,
            ]);

        } catch (\Exception $e) {
            Log::error('Upload failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal upload file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus file sementara.
     */
    public function destroy(Request $request, $id = null): JsonResponse
    {
        // Handle path dari body (plain text) atau URL param
        $filePath = trim($request->getContent()) ?: $id;

        Log::info('Delete request for path: ' . $filePath);

        if (! $filePath || ! Storage::disk('public')->exists($filePath)) {
            Log::warning('File not found: ' . $filePath);
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        try {
            // Hapus file
            Storage::disk('public')->delete($filePath);

            // Hapus folder jika kosong
            $directory = dirname($filePath);
            if (Storage::disk('public')->exists($directory)) {
                $remainingFiles = Storage::disk('public')->files($directory, false); // Non-recursive
                if (empty($remainingFiles)) {
                    Storage::disk('public')->deleteDirectory($directory);
                    Log::info('Empty temp directory deleted: ' . $directory);
                }
            }

            Log::info('Temporary file deleted successfully: ' . $filePath);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Delete failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal hapus file'], 500);
        }
    }
}
