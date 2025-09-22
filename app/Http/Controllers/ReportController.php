<?php
namespace App\Http\Controllers;

// Models and Events
use App\Events\ReportStatusUpdated;
use App\Models\Report;
use App\Models\ReportImage; // Pastikan model ini di-import
// Laravel Facades & Helpers
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
// Intervention Image
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ReportController extends Controller
{
    /**
     * Menampilkan form untuk membuat laporan baru.
     */
    public function create(): View
    {
        return view('resident.laporan.create');
    }

    /**
     * Menyimpan laporan baru ke database.
     */
    public function store(Request $request): JsonResponse
    {

        // PERBAIKAN: Parse images jika JSON string (dari JS FormData)
        $images = $request->input('images');
        if (is_string($images)) {
            $decodedImages = json_decode($images, true); // Parse ke array
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                $request->merge(['images' => $decodedImages]); // Ganti request dengan array
                Log::info('Images parsed from JSON string:', $decodedImages);
            } else {
                Log::error('Invalid JSON for images: ' . $images);
                return response()->json(['error' => 'Format images tidak valid'], 422);
            }
        }
        // PERBAIKAN: Parse video_thumbnails jika ada (array base64)
        $videoThumbnails = [];
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'video_thumbnails') === 0 && ! empty($value)) {
                $videoThumbnails[] = $value; // Collect base64 thumbnails
            }
        }
        if (! empty($videoThumbnails)) {
            $request->merge(['video_thumbnails' => $videoThumbnails]);
            Log::info('Video thumbnails collected:', count($videoThumbnails));
        }
        // PERBAIKAN UTAMA: Validasi diubah untuk menerima array of strings (path)
        // Validasi
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'required|string',
            'location_address'   => 'required|string|max:500',
            'images'             => 'required|array|min:1|max:5',
            'images.*'           => 'string',
            'video_thumbnails'   => 'sometimes|array',
            'video_thumbnails.*' => 'nullable|string',
        ], [
            'images.required' => 'Anda harus mengunggah minimal satu file bukti.',
            'images.min'      => 'Anda harus mengunggah minimal satu file bukti.',
            'images.max'      => 'Anda hanya dapat mengunggah maksimal 5 file.',
        ]);

        // Debug data yang diterima (opsional, bisa dihapus setelah testing)
        Log::info('Received data:', $validated);

        try {
            DB::beginTransaction();

            $reportCode = 'PKRP-' . date('ymd') . '-' . strtoupper(Str::random(4));
            $report     = Report::create([
                'report_code'      => $reportCode,
                'resident_id'      => Auth::id(),
                'title'            => $validated['title'],
                'description'      => $validated['description'],
                'location_address' => $validated['location_address'],
                'status'           => 'pending',
                'source'           => 'web',
                'source_contact'   => Auth::user()->phone_number,
            ]);

            foreach ($validated['images'] as $index => $tempPath) {
                if (! Storage::disk('public')->exists($tempPath)) {
                    continue;
                }

                $fileType     = Str::startsWith(Storage::disk('public')->mimeType($tempPath), 'video') ? 'video' : 'image';
                $filename     = basename($tempPath);
                $permanentDir = 'reports/' . $report->id;
                $newFullPath  = $permanentDir . '/' . $filename;

                Storage::disk('public')->move($tempPath, $newFullPath);

                $thumbnailPath = null;
                $thumbnailDir  = $permanentDir . '/thumbnails';

                if ($fileType === 'image') {
                    $manager       = new ImageManager(new Driver());
                    $thumbnailName = 'thumb_' . Str::random(10) . '.jpg';
                    Storage::disk('public')->makeDirectory($thumbnailDir);
                    $image = $manager->read(Storage::disk('public')->path($newFullPath));
                    $image->cover(300, 200)->toJpeg(80)->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                    $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
                } elseif ($fileType === 'video' && ! empty($validated['video_thumbnails'][$index])) {
                    $base64_image       = $validated['video_thumbnails'][$index];
                    @list(, $file_data) = explode(',', $base64_image);
                    $thumbnailData      = base64_decode($file_data);

                    $thumbnailName = 'thumb_' . Str::random(10) . '.jpg';
                    Storage::disk('public')->makeDirectory($thumbnailDir);
                    Storage::disk('public')->put($thumbnailDir . '/' . $thumbnailName, $thumbnailData);
                    $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
                }

                $report->images()->create([
                    'file_path'      => $newFullPath,
                    'file_type'      => $fileType,
                    'thumbnail_path' => $thumbnailPath,
                ]);

                Storage::disk('public')->deleteDirectory(dirname($tempPath));
            }

            DB::commit();
            event(new ReportStatusUpdated($report->fresh()));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan laporan baru: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message'      => 'Laporan Anda berhasil dikirim! Kode Laporan: ' . $reportCode,
            'redirect_url' => route('dashboard'),
        ]);
    }

    /**
     * Menampilkan detail laporan.
     */
    public function show(Request $request, Report $report): View
    {
        // Otorisasi (tetap sama)
        if (Auth::id() !== $report->resident_id && ! in_array(Auth::user()->role->name, ['super-admin', 'admin-officer', 'field-officer'])) {
            abort(403, 'ANDA TIDAK BERHAK MENGAKSES LAPORAN INI.');
        }

        if ($request->has('from_update')) {
            session()->flash('success', 'Laporan berhasil diperbarui!');
        }

        // EAGER LOADING (Diperbarui untuk memuat semua relasi yang kita butuhkan)
        $report->load('images', 'resident', 'followUp.media', 'statusHistories.user', 'followUp.officers');

        // Menampilkan View (tetap sama)
        return view('resident.laporan.show', [
            'report' => $report,
        ]);
    }

    /**
     * Menampilkan form edit laporan.
     */
    public function edit(Report $report): View
    {
        // OTORISASI: Pastikan hanya pemilik laporan & statusnya 'pending' yang bisa mengedit.
        if (Auth::id() !== $report->resident_id || $report->status !== 'pending') {
            abort(403, 'LAPORAN INI TIDAK DAPAT DIUBAH LAGI.');
        }

        // Muat relasi gambar untuk ditampilkan di form
        $report->load('images');

        return view('resident.laporan.edit', ['report' => $report]);
    }

    /**
     * Memperbarui data laporan di database.
     */
    public function update(Request $request, Report $report): RedirectResponse
    {
        if (Auth::id() !== $report->resident_id || $report->status !== 'pending') {
            abort(403, 'LAPORAN INI TIDAK DAPAT DIUBAH LAGI.');
        }

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'required|string',
            'location_address'   => 'required|string|max:500',
            'images'             => 'nullable|array|max:5', // File baru tidak wajib
            'images.*'           => 'string',               // Kunci: validasi sebagai string
            'video_thumbnails'   => 'sometimes|array',
            'video_thumbnails.*' => 'nullable|string',
            'delete_images'      => 'nullable|array',
            'delete_images.*'    => 'integer|exists:report_images,id',
        ]);

        try {
            DB::beginTransaction();
            $report->update(Arr::only($validated, ['title', 'description', 'location_address']));

            if (! empty($validated['delete_images'])) {
                $imagesToDelete = $report->images()->whereIn('id', $validated['delete_images'])->get();
                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->file_path);
                    if ($image->thumbnail_path) {
                        Storage::disk('public')->delete($image->thumbnail_path);
                    }
                    $image->delete();
                }
            }

            if (! empty($validated['images'])) {
                foreach ($validated['images'] as $index => $tempPath) {
                    if (! Storage::disk('public')->exists($tempPath)) {
                        continue;
                    }

                    $fileType     = Str::startsWith(Storage::disk('public')->mimeType($tempPath), 'video') ? 'video' : 'image';
                    $filename     = basename($tempPath);
                    $permanentDir = 'reports/' . $report->id;
                    $newFullPath  = $permanentDir . '/' . $filename;
                    Storage::disk('public')->move($tempPath, $newFullPath);

                    $thumbnailPath = null;
                    $thumbnailDir  = $permanentDir . '/thumbnails';

                    if ($fileType === 'image') {
                        $manager       = new ImageManager(new Driver());
                        $thumbnailName = 'thumb_' . Str::random(10) . '.jpg';
                        Storage::disk('public')->makeDirectory($thumbnailDir);
                        $image = $manager->read(Storage::disk('public')->path($newFullPath));
                        $image->cover(300, 200)->toJpeg(80)->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                        $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
                    } elseif ($fileType === 'video' && ! empty($validated['video_thumbnails'][$index])) {
                        // ... logika untuk menyimpan thumbnail video ...
                    }

                    $report->images()->create([
                        'file_path'      => $newFullPath,
                        'file_type'      => $fileType,
                        'thumbnail_path' => $thumbnailPath,
                    ]);

                    Storage::disk('public')->deleteDirectory(dirname($tempPath));
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate laporan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui laporan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('laporan.show', $report)->with('success', 'Laporan berhasil diperbarui!');
    }
}
