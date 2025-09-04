<?php
namespace App\Http\Controllers;

// Model
use App\Events\ReportStatusUpdated;
use App\Models\Report;
// Laravel Facades & Helpers
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// Intervention Image & FFMpeg
use Illuminate\Support\Str;
use Illuminate\View\View;
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
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input untuk multi-upload
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'location_address' => 'required|string|max:500',
            'images'           => 'required|array|max:5',                          // 'images' sekarang adalah array
            'images.*'         => 'file|mimes:jpeg,png,jpg,mp4,mov,avi|max:25600', // Validasi setiap file
        ], [
            'images.required' => 'Anda harus mengunggah minimal satu file (gambar atau video).',
            'images.array'    => 'Format unggahan tidak valid.',
            'images.max'      => 'Anda hanya dapat mengunggah maksimal 5 file.',
            'images.*.mimes'  => 'Hanya file gambar (jpeg, png, jpg) dan video (mp4, mov, avi) yang diizinkan.',
            'images.*.max'    => 'Ukuran maksimal setiap file adalah 25MB.',
        ]);

        try {
            DB::beginTransaction();

            // 2. Buat Laporan Utama Terlebih Dahulu
            $reportCode = 'PKRP-' . date('ymd') . '-' . strtoupper(Str::random(4));
            $report     = Report::create([
                'report_code'      => $reportCode,
                'resident_id'      => Auth::id(),
                'title'            => $validated['title'],
                'description'      => $validated['description'],
                'location_address' => $validated['location_address'],
                'status'           => 'pending',
                'source'           => 'web',                      // Menandai bahwa laporan ini berasal dari web
                'source_contact'   => Auth::user()->phone_number, // Ambil no HP user yg login
            ]);

            // 3. Proses Setiap File yang Diupload
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $filename      = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $fileType      = str_starts_with($file->getMimeType(), 'image') ? 'image' : 'video';
                    $path          = 'reports/' . $fileType . 's';
                    $thumbnailPath = null; // Inisialisasi thumbnail path

                    if (! Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->makeDirectory($path);
                    }

                    if ($fileType === 'image') {
                        // Proses GAMBAR
                        $manager = new ImageManager(new Driver());
                        $image   = $manager->read($file);
                        $image->scale(width: 800)->toJpeg(75)->save(storage_path('app/public/' . $path . '/' . $filename));
                    } else {
                        // Proses VIDEO
                        try {
                            $ffmpeg = FFMpeg::create([
                                'ffmpeg.binaries'  => env('FFMPEG_PATH', 'ffmpeg'),
                                'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),
                            ]); // Konfigurasi FFMpeg-mu

                            // === MEMBUAT THUMBNAIL DARI VIDEO ===
                            $video         = $ffmpeg->open($file->getPathname());
                            $thumbnailName = 'thumb_' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                            $thumbnailDir  = 'reports/thumbnails';
                            if (! Storage::disk('public')->exists($thumbnailDir)) {
                                Storage::disk('public')->makeDirectory($thumbnailDir);
                            }
                            $video->frame(TimeCode::fromSeconds(1))->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                            $thumbnailPath = $thumbnailDir . '/' . $thumbnailName; // Simpan path thumbnail
                                                                                   // ===================================

                            // Kompres Video
                            $format = new X264('aac', 'libx264');
                            $format->setKiloBitrate(500);
                            $video->save($format, storage_path('app/public/' . $path . '/' . $filename));
                        } catch (\Exception $e) {
                            Log::warning('FFMpeg Gagal, menyimpan file video asli: ' . $e->getMessage());
                            $file->storeAs($path, $filename, 'public');
                        }
                    }

                    // Simpan path dan tipe file ke DB
                    $report->images()->create([
                        'file_path'      => $path . '/' . $filename,
                        'file_type'      => $fileType,
                        'thumbnail_path' => $thumbnailPath, // <-- Simpan path thumbnail
                    ]);
                }
            }

            DB::commit();

            event(new ReportStatusUpdated($report->fresh()));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan laporan baru: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('dashboard')->with('success', 'Laporan Anda berhasil dikirim! Kode Laporan: ' . $reportCode);
    }

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
        $report->load('images', 'resident', 'followUp.media', 'statusHistories');

        // Menampilkan View (tetap sama)
        return view('resident.laporan.show', [
            'report' => $report,
        ]);
    }

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
        // OTORISASI: Cek ulang sebelum menyimpan
        if (Auth::id() !== $report->resident_id || $report->status !== 'pending') {
            abort(403, 'LAPORAN INI TIDAK DAPAT DIUBAH LAGI.');
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'location_address' => 'required|string|max:500',
            'images'           => 'nullable|array|max:5', // Gambar baru tidak wajib
            'images.*'         => 'file|mimes:jpeg,png,jpg,mp4,mov,avi|max:25600',
            'delete_images'    => 'nullable|array', // Array berisi ID gambar yang akan dihapus
            'delete_images.*'  => 'integer|exists:report_images,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update data teks
            $report->update([
                'title'            => $validated['title'],
                'description'      => $validated['description'],
                'location_address' => $validated['location_address'],
            ]);

            // 2. Hapus gambar lama (jika ada)
            if (! empty($validated['delete_images'])) {
                foreach ($validated['delete_images'] as $imageId) {
                    $image = $report->images()->find($imageId);
                    if ($image) {
                        Storage::disk('public')->delete($image->file_path);
                        if ($image->thumbnail_path) {
                            Storage::disk('public')->delete($image->thumbnail_path);
                        }
                        $image->delete();
                    }
                }
            }

            // 3. Proses Setiap File yang Diupload
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $filename      = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $fileType      = str_starts_with($file->getMimeType(), 'image') ? 'image' : 'video';
                    $path          = 'reports/' . $fileType . 's';
                    $thumbnailPath = null; // Inisialisasi thumbnail path

                    if (! Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->makeDirectory($path);
                    }

                    if ($fileType === 'image') {
                        // Proses GAMBAR
                        $manager = new ImageManager(new Driver());
                        $image   = $manager->read($file);
                        $image->scale(width: 800)->toJpeg(75)->save(storage_path('app/public/' . $path . '/' . $filename));
                    } else {
                        // Proses VIDEO
                        try {
                            $ffmpeg = FFMpeg::create([
                                'ffmpeg.binaries'  => env('FFMPEG_PATH', 'ffmpeg'),
                                'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),
                            ]); // Konfigurasi FFMpeg-mu

                            // === MEMBUAT THUMBNAIL DARI VIDEO ===
                            $video         = $ffmpeg->open($file->getPathname());
                            $thumbnailName = 'thumb_' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                            $thumbnailDir  = 'reports/thumbnails';
                            if (! Storage::disk('public')->exists($thumbnailDir)) {
                                Storage::disk('public')->makeDirectory($thumbnailDir);
                            }
                            $video->frame(TimeCode::fromSeconds(1))->save(storage_path('app/public/' . $thumbnailDir . '/' . $thumbnailName));
                            $thumbnailPath = $thumbnailDir . '/' . $thumbnailName; // Simpan path thumbnail
                                                                                   // ===================================

                            // Kompres Video
                            $format = new X264('aac', 'libx264');
                            $format->setKiloBitrate(500);
                            $video->save($format, storage_path('app/public/' . $path . '/' . $filename));
                        } catch (\Exception $e) {
                            Log::warning('FFMpeg Gagal, menyimpan file video asli: ' . $e->getMessage());
                            $file->storeAs($path, $filename, 'public');
                        }
                    }

                    // Simpan path dan tipe file ke DB
                    $report->images()->create([
                        'file_path'      => $path . '/' . $filename,
                        'file_type'      => $fileType,
                        'thumbnail_path' => $thumbnailPath, // <-- Simpan path thumbnail
                    ]);
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
