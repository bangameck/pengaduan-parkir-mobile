<?php
namespace App\Http\Controllers;

use App\Events\ReportStatusUpdated;
use App\Models\Report;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ReportController extends Controller
{
    public function create(): View
    {
        return view('resident.laporan.create');
    }

    public function store(Request $request): JsonResponse
    {
        $images = json_decode($request->input('images'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Format data gambar tidak valid.'], 422);
        }
        $request->merge(['images' => is_array($images) ? $images : []]);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'location_address' => 'required|string|max:500',
            'images'           => 'required|array|min:1|max:5',
            'images.*'         => 'string',
        ], ['images.required' => 'Anda harus mengunggah minimal satu file bukti.']);

        try {
            DB::beginTransaction();
            $report = Report::create([
                'report_code'      => 'PKRP-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                'resident_id'      => Auth::id(),
                'title'            => $validated['title'],
                'description'      => $validated['description'],
                'location_address' => $validated['location_address'],
                'status'           => 'pending', 'source' => 'web',
                'source_contact'   => Auth::user()->phone_number,
            ]);

            // Menggunakan logika yang sama dengan update()
            if (! empty($validated['images'])) {
                $this->processFiles($validated['images'], $report);
            }

            DB::commit();
            event(new ReportStatusUpdated($report->fresh()));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan laporan baru: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return response()->json(['message' => 'Gagal membuat laporan: Terjadi kesalahan di server.'], 500);
        }
        return response()->json([
            'message'      => 'Laporan Anda berhasil dikirim! Kode Laporan: ' . $report->report_code,
            'redirect_url' => route('dashboard'),
        ]);
    }

    public function show(Report $report): View
    {
        if (Auth::id() !== $report->resident_id && ! in_array(Auth::user()->role->name, ['super-admin', 'admin-officer', 'field-officer'])) {
            abort(403, 'ANDA TIDAK BERHAK MENGAKSES LAPORAN INI.');
        }
        $report->load('images', 'resident', 'followUp.media', 'statusHistories.user', 'followUp.officers');
        return view('resident.laporan.show', ['report' => $report]);
    }

    public function edit(Report $report): View
    {
        if (Auth::id() !== $report->resident_id || $report->status !== 'pending') {
            abort(403, 'LAPORAN INI TIDAK DAPAT DIUBAH LAGI.');
        }
        $report->load('images');
        return view('resident.laporan.edit', ['report' => $report]);
    }

    public function update(Request $request, Report $report): JsonResponse
    {
        if (Auth::id() !== $report->resident_id || $report->status !== 'pending') {
            return response()->json(['message' => 'Laporan ini tidak dapat diubah lagi.'], 403);
        }

        $newImages = json_decode($request->input('images', '[]'), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($newImages)) {
            $request->merge(['images' => $newImages]);
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'location_address' => 'required|string|max:500',
            'images'           => 'nullable|array',
            'images.*'         => 'string',
            'delete_images'    => 'nullable|array',
            'delete_images.*'  => 'integer|exists:report_images,id',
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
                $this->processFiles($validated['images'], $report);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate laporan: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui laporan: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message'      => 'Laporan berhasil diperbarui!',
            'redirect_url' => route('laporan.show', $report) . '?from_update=1',
        ]);
    }

    // Dibuat private function agar tidak duplikasi kode antara store dan update
    private function processFiles(array $tempPaths, Report $report): void
    {
        $permanentDir = 'reports/' . $report->id;
        $thumbnailDir = $permanentDir . '/thumbnails';
        Storage::disk('public')->makeDirectory($thumbnailDir);

        foreach ($tempPaths as $tempPath) {
            if (! Storage::disk('public')->exists($tempPath)) {
                continue;
            }

            $absoluteTempPath = Storage::disk('public')->path($tempPath);
            $fileType         = Str::startsWith(Storage::disk('public')->mimeType($tempPath), 'video') ? 'video' : 'image';
            $filename         = basename($tempPath);
            $newFullPath      = $permanentDir . '/' . $filename;
            $thumbnailPath    = null;

            if ($fileType === 'image') {
                $manager = new ImageManager(new Driver());
                $image   = $manager->read($absoluteTempPath);
                $image->scale(width: 800)->toJpeg(75)->save(storage_path('app/public/' . $newFullPath));
                $thumbnailName = 'thumb_' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                $image->cover(300, 200)->toJpeg(80)->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
            } else { // Proses VIDEO
                try {
                    $ffmpeg = FFMpeg::create([
                        'ffmpeg.binaries'  => env('FFMPEG_PATH', 'ffmpeg'),
                        'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),
                    ]);
                    $video         = $ffmpeg->open($absoluteTempPath);
                    $thumbnailName = 'thumb_' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                    $video->frame(TimeCode::fromSeconds(1))->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                    $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
                    $format        = new X264('aac', 'libx264');
                    $format->setKiloBitrate(500);
                    $video->save($format, storage_path('app/public/' . $newFullPath));
                } catch (\Exception $e) {
                    Log::error('FFMpeg Gagal: ' . $e->getMessage() . ' untuk file ' . $tempPath);
                    Storage::disk('public')->move($tempPath, $newFullPath);
                }
            }

            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
            if (empty(Storage::disk('public')->files(dirname($tempPath)))) {
                Storage::disk('public')->deleteDirectory(dirname($tempPath));
            }
            $report->images()->create([
                'file_path'      => $newFullPath,
                'file_type'      => $fileType,
                'thumbnail_path' => $thumbnailPath,
            ]);
        }
    }
}
