<?php
namespace App\Http\Controllers\Field;

use App\Events\ReportStatusUpdated; // <-- 1. IMPORT EVENT
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class FieldReportController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $query  = Report::query()->where('status', 'verified');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('report_code', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%');
            });
        }

        $reports = $query->with('resident')
            ->latest('verified_at')
            ->paginate(10)
            ->withQueryString();

        return view('field.laporan.index', [
            'reports' => $reports,
            'search'  => $search,
        ]);
    }

    public function createFollowUp(Report $report): View
    {
        $report->load('resident', 'images');

        // Ambil semua user yang rolenya 'field-officer' untuk ditampilkan di form
        $fieldOfficers = User::whereHas('role', function ($query) {
            $query->where('name', 'field-officer');
        })->get();

        return view('field.laporan.follow-up', [
            'report'        => $report,
            'fieldOfficers' => $fieldOfficers,
        ]);
    }

    public function storeFollowUp(Request $request, Report $report): JsonResponse
    {
        $proofMedia = json_decode($request->input('proof_media'), true);
        $request->merge(['proof_media' => is_array($proofMedia) ? $proofMedia : []]);

        $validated = $request->validate([
            'notes'                => 'required|string|min:10',
            'officer_ids'          => 'required|array',
            'officer_ids.*'        => 'exists:users,id',
            'proof_media'          => 'required|array|max:5',
            'proof_media.*'        => 'string', // Validasi sebagai string path
            'latitude'             => 'required|numeric',
            'longitude'            => 'required|numeric',
            'location_description' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $followUp = $report->followUp()->create([
                'notes'                => $validated['notes'],
                'proof_latitude'       => $validated['latitude'],
                'proof_longitude'      => $validated['longitude'],
                'location_description' => $validated['location_description'],
            ]);
            $followUp->officers()->attach($validated['officer_ids']);

            if (! empty($validated['proof_media'])) {
                $this->processProofFiles($validated['proof_media'], $followUp);
            }

            $report->update(['status' => 'completed', 'completed_at' => now()]);
            DB::commit();
            event(new ReportStatusUpdated($report->fresh()));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan tindak lanjut: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message'      => 'Tindak lanjut untuk laporan ' . $report->report_code . ' berhasil disimpan!',
            'redirect_url' => route('petugas.tugas.index'),
        ]);
    }

    private function processProofFiles(array $tempPaths, $followUp): void
    {
        // Logika ini sama persis dengan 'processFiles' di controller lain,
        // hanya berbeda nama folder tujuan (proofs/)
        $permanentDir = 'proofs/' . $followUp->id;
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
                // Buat thumbnail juga untuk gambar bukti
                $thumbnailName = 'thumb_' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                $image->cover(300, 200)->toJpeg(80)->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
            } else {
                try {
                    $ffmpeg        = FFMpeg::create(['ffmpeg.binaries' => env('FFMPEG_PATH', 'ffmpeg'), 'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe')]);
                    $video         = $ffmpeg->open($absoluteTempPath);
                    $thumbnailName = 'thumb_' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                    $video->frame(TimeCode::fromSeconds(1))->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                    $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
                    $format        = new X264('aac', 'libx264');
                    $format->setKiloBitrate(500);
                    $video->save($format, storage_path('app/public/' . $newFullPath));
                } catch (\Exception $e) {
                    Log::error('FFMpeg Gagal (Follow-up): ' . $e->getMessage());
                    Storage::disk('public')->move($tempPath, $newFullPath);
                }
            }

            $followUp->media()->create(['file_path' => $newFullPath, 'file_type' => $fileType, 'thumbnail_path' => $thumbnailPath]);

            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }

            if (empty(Storage::disk('public')->files(dirname($tempPath)))) {
                Storage::disk('public')->deleteDirectory(dirname($tempPath));
            }

        }
    }
}
