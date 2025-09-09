<?php
namespace App\Http\Controllers\Field;

use App\Events\ReportStatusUpdated; // <-- 1. IMPORT EVENT
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function storeFollowUp(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'notes'         => 'required|string|min:10',
            'officer_ids'   => 'required|array', // <-- Input baru untuk banyak petugas
            'officer_ids.*' => 'exists:users,id',
            'proof_media'   => 'required|array|max:5',
            'proof_media.*' => 'file|mimes:jpeg,png,jpg,mp4,mov,avi|max:25600',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            // Buat record tindak lanjut dulu (tanpa officer_id)
            $followUp = $report->followUp()->create([
                'notes'           => $validated['notes'],
                'proof_latitude'  => $validated['latitude'],
                'proof_longitude' => $validated['longitude'],
            ]);

            // LAMPIRKAN BANYAK PETUGAS ke tindak lanjut ini
            $followUp->officers()->attach($validated['officer_ids']);

            if ($request->hasFile('proof_media')) {
                foreach ($request->file('proof_media') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $fileType = str_starts_with($file->getMimeType(), 'image') ? 'image' : 'video';
                    $path     = 'proofs/' . $fileType . 's';

                    $thumbnailPath = null;

                    if (! Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->makeDirectory($path);
                    }

                    if ($fileType === 'image') {
                        $manager = new ImageManager(new Driver());
                        $image   = $manager->read($file);
                        $image->scale(width: 800)->toJpeg(75)->save(storage_path('app/public/' . $path . '/' . $filename));
                    } else {
                        try {
                            // Tes terakhir dengan path eksplisit
                            $ffmpeg = FFMpeg::create([
                                'ffmpeg.binaries'  => env('FFMPEG_PATH', 'ffmpeg'),
                                'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),
                            ]);
                            $video = $ffmpeg->open($file->getPathname());
                            // === MEMBUAT THUMBNAIL DARI VIDEO ===
                            $thumbnailName = 'thumb_' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                            $thumbnailDir  = 'proofs/thumbnails';
                            if (! Storage::disk('public')->exists($thumbnailDir)) {
                                Storage::disk('public')->makeDirectory($thumbnailDir);
                            }
                            $video->frame(TimeCode::fromSeconds(1))->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                            $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
                            // ===================================
                            $format = new X264('aac', 'libx264');
                            $format->setKiloBitrate(500);
                            $video->save($format, storage_path('app/public/' . $path . '/' . $filename));
                        } catch (\Exception $e) {
                            Log::warning('FFMpeg Gagal (bahkan dengan hardcode path), menyimpan file video asli: ' . $e->getMessage());
                            $file->storeAs($path, $filename, 'public');
                        }
                    }

                    $followUp->media()->create(['file_path' => $path . '/' . $filename, 'file_type' => $fileType, 'thumbnail_path' => $thumbnailPath]);
                }
            }

            $report->update([
                'status'           => 'completed',
                'field_officer_id' => Auth::id(),
                'completed_at'     => now(),

            ]);

            DB::commit();

            // <-- 2. PICU EVENT SETELAH COMMIT BERHASIL
            event(new ReportStatusUpdated($report->fresh()));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan tindak lanjut: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('petugas.tugas.index')->with('success', 'Tindak lanjut untuk laporan ' . $report->report_code . ' berhasil disimpan!');
    }
}
