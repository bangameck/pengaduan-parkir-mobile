<?php
namespace App\Http\Controllers\Admin;

use App\Events\ReportStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdminReportController extends Controller
{
    /**
     * Menampilkan daftar laporan yang perlu diverifikasi.
     */
    public function index(Request $request)
    {
        // 1. DAPATKAN STATUS AKTIF DARI URL, DEFAULT-NYA 'pending'
        $activeStatus = $request->query('status', 'pending');

        // 2. HITUNG JUMLAH LAPORAN UNTUK SETIAP TAB (COUNT_NUMBER)
        $reportCounts = [
            'pending'   => Report::where('status', 'pending')->count(),
            'completed' => Report::where('status', 'completed')->count(),
            'rejected'  => Report::where('status', 'rejected')->count(),
            'all'       => Report::count(), // 'all' untuk Seluruh Laporan
        ];

        // Ambil query pencarian
        $search = $request->query('search');

        // 3. MULAI MEMBANGUN QUERY UTAMA
        $query = Report::query();

        // Terapkan filter status berdasarkan tab yang aktif
        if ($activeStatus && $activeStatus !== 'all') {
            $query->where('status', $activeStatus);
        }
        // Jika tab 'all' aktif, tidak perlu filter status

        // Terapkan filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('report_code', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%');
            });
        }

        // Ambil data laporan
        $reports = $query->with('resident')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // 4. KIRIM SEMUA DATA BARU KE VIEW
        return view('admin.laporan.index', [
            'reports'      => $reports,
            'search'       => $search,
            'reportCounts' => $reportCounts, // Kirim data jumlah laporan
            'activeStatus' => $activeStatus, // Kirim status yang sedang aktif
        ]);
    }

    /**
     * Proses verifikasi laporan.
     * (Akan kita isi nanti)
     */
    public function verify(Report $report)
    {
        $report->update([
            'status'           => 'verified',
            'admin_officer_id' => Auth::id(),
            'verified_at'      => now(),
        ]);

        event(new ReportStatusUpdated($report->fresh()));

        return redirect()->route('admin.laporan.index')->with('success', 'Laporan ' . $report->report_code . ' berhasil diverifikasi.');
    }

/**
 * Proses menolak laporan.
 */
    public function reject(Request $request, Report $report)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $report->update([
            'status'           => 'rejected',
            'admin_officer_id' => Auth::id(),
            'verified_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        event(new ReportStatusUpdated($report->fresh()));

        return redirect()->route('admin.laporan.index')->with('success', 'Laporan ' . $report->report_code . ' berhasil ditolak.');
    }

    /**
     * Menampilkan form untuk membuat laporan baru oleh admin.
     */
    public function create()
    {
        return view('admin.laporan.create');
    }

    /**
     * Menyimpan laporan baru dari form yang diinput admin.
     * (Logikanya akan kita buat nanti, sekarang siapkan saja dulu)
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // 1. KITA DEFINISIKAN ATURAN VALIDASI
        $validationRules = [
            'source'           => 'required', 'string', Rule::in(['whatsapp', 'facebook', 'tiktok', 'instagram', 'lainnya']),
            'resident_name'    => 'required_if:source,whatsapp', 'nullable', 'string', 'max:255',
            'source_contact'   => 'required', 'string', 'max:255',
            'title'            => 'required', 'string', 'max:255',
            'description'      => 'required', 'string', 'min:10', // <-- SAYA LONGGARKAN JADI 10 KARAKTER
            'location_address' => 'required', 'string', 'max:255',
            'images'           => 'required|array|max:5',                          // 'images' sekarang adalah array
            'images.*'         => 'file|mimes:jpeg,png,jpg,mp4,mov,avi|max:25600', // Validasi setiap file
        ];

        // 2. KITA BUAT PESAN ERROR KUSTOM DALAM BAHASA INDONESIA
        $validationMessages = [
            'required'                  => 'Kolom :attribute wajib diisi.',
            'min'                       => 'Kolom :attribute minimal harus :min karakter.',
            'max'                       => [
                'string' => 'Kolom :attribute maksimal harus :max karakter.',
                'array'  => 'Anda hanya boleh mengupload maksimal :max file.',
            ],
            'images.required'           => 'Anda harus mengunggah minimal satu file (gambar atau video).',
            'images.array'              => 'Format unggahan tidak valid.',
            'images.max'                => 'Anda hanya dapat mengunggah maksimal 5 file.',
            'images.*.mimes'            => 'Hanya file gambar (jpeg, png, jpg) dan video (mp4, mov, avi) yang diizinkan.',
            'images.*.max'              => 'Ukuran maksimal setiap file adalah 25MB.',
            'source_contact.required'   => 'Detail kontak (nomor WA/username) wajib diisi.',
            'resident_name.required_if' => 'Nama pelapor wajib diisi jika sumbernya WhatsApp.',
        ];

        // 3. JALANKAN VALIDASI DENGAN ATURAN DAN PESAN KUSTOM
        $validated = $request->validate($validationRules, $validationMessages);

        // =================================================================
        // SISA LOGIKA DI BAWAH INI SAMA PERSIS SEPERTI SEBELUMNYA, TIDAK PERLU DIUBAH
        // =================================================================
        DB::beginTransaction();
        try {
            $resident       = null;
            $residentRoleId = Role::where('name', 'resident')->first()->id;

            if ($validated['source'] === 'whatsapp') {
                // Jika sumbernya WA, cari user berdasarkan nomor HP. Jika tidak ada, buat baru.
                $resident = User::firstOrCreate(
                    ['phone_number' => $validated['source_contact']],
                    [
                        'name'     => $validated['resident_name'],
                        'username' => Str::slug($validated['resident_name'], '_') . '_' . strtolower(Str::random(4)),
                        'email'    => 'wa.' . $validated['source_contact'] . '@parkirapp.dev', // Email unik dummy berbasis no hp
                        'password' => bcrypt(Str::random(16)),
                        'role_id'  => $residentRoleId,
                    ]
                );
            } else {
                // Jika sumbernya BUKAN WA (Medsos, dll), kita selalu buat user "tamu" baru.
                $resident = User::create([
                    'name'     => "Pelapor via " . ucfirst($validated['source']),
                    'username' => Str::slug($validated['source_contact'], '_') . '_' . strtolower(Str::random(4)),
                    'email'    => 'guest.' . time() . Str::random(5) . '@parkirapp.dev', // Email unik dummy
                    'password' => bcrypt(Str::random(16)),
                    'role_id'  => $residentRoleId,
                    // 'phone_number' di sini sengaja dikosongkan karena sumbernya bukan WA
                ]);
            }

            $report = Report::create([
                'resident_id'      => $resident->id,
                'admin_officer_id' => Auth::id(),
                'report_code'      => 'PKRP-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                'title'            => $validated['title'],
                'description'      => $validated['description'],
                'location_address' => $validated['location_address'],
                'status'           => 'pending',
                'source'           => $validated['source'],
                'source_contact'   => $validated['source_contact'],
            ]);

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

            if ($validated['source'] === 'whatsapp') {
                $this->sendWhatsAppConfirmation($report);
            }

            return redirect()->route('admin.laporan.index')->with('success', "Laporan #{$report->report_code} berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal membuat laporan oleh admin: " . $e->getMessage() . ' di baris ' . $e->getLine());
            return back()->with('error', 'Terjadi kesalahan internal saat menyimpan laporan. Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Method terpisah untuk mengirim notifikasi WhatsApp via Fonnte
     */
    private function sendWhatsAppConfirmation(Report $report)
    {
        if (! $report->source_contact) {
            return;
        }

        try {
            $reportUrl = route('public.laporan.show', $report);
            $message   = "✅ Laporan Anda telah kami terima dan input oleh admin.\n\n"
                . "Kode Laporan: *{$report->report_code}*\n"
                . "Judul: {$report->title}\n\n"
                . "Kami Juga Sudah membuat user login anda dengan menambahkan nomor : *{$report->source_contact}*\n"
                . "Silahkan klik tulisan *lupa password* pada halaman login untuk membuat password baru\n\n"
                . "Anda dapat memantau status laporan melalui link berikut:\n"
                . $reportUrl . "\n\n"
                . "Terima kasih atas partisipasi Anda.";

            Http::withHeaders(['Authorization' => config('fonnte_token')])
                ->post('https://api.fonnte.com/send', [
                    'target'  => $report->source_contact,
                    'message' => $message,
                ]);

        } catch (\Exception $e) {
            // Jika gagal kirim WA, jangan hentikan proses. Cukup catat errornya.
            Log::error('Gagal mengirim WhatsApp konfirmasi via Fonnte: ' . $e->getMessage());
        }
    }
}
