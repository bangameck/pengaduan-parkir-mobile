<?php
namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportFollowUp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View | RedirectResponse
    {
        $user   = Auth::user();
        $userId = Auth::id();

        // Router Cerdas: Memilih dashboard berdasarkan role pengguna
        switch ($user->role->name) {
            case 'super-admin':
                                          // Nanti kita buatkan dashboard khusus untuk Super Admin
                return view('dashboard'); // Sementara pakai view default

            case 'admin-officer':
                // Siapkan data untuk dashboard Admin Officer
                $stats = [
                    'pending'        => Report::where('status', 'pending')->count(),
                    'verified_today' => Report::where('admin_officer_id', $user->id)
                        ->where('status', 'verified')
                        ->whereDate('verified_at', today())
                        ->count(),
                    'rejected_today' => Report::where('admin_officer_id', $user->id)
                        ->where('status', 'rejected')
                        ->whereDate('verified_at', today())
                        ->count(),
                ];

                // 2. Data untuk Grafik Tren Laporan Masuk (7 Hari Terakhir)
                $trendData = Report::query()
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                    ->where('created_at', '>=', now()->subDays(6))
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get();

                $trendChart = [
                    'labels' => $trendData->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->isoFormat('dddd, D M')),
                    'values' => $trendData->pluck('count'),
                ];

                // 3. Data untuk Grafik Proporsi Sumber Laporan
                $sourceData = Report::query()
                    ->select('source', DB::raw('count(*) as count'))
                    ->groupBy('source')
                    ->get();

                $sourceChart = [
                    'labels' => $sourceData->pluck('source')->map(fn($source) => ucfirst($source)),
                    'values' => $sourceData->pluck('count'),
                ];

                // 4. Daftar Laporan Terbaru yang Perlu Diverifikasi
                $recentPendingReports = Report::where('status', 'pending')
                    ->with('resident')
                    ->latest()
                    ->take(5)
                    ->get();

                // Tampilkan view khusus Admin Officer dengan semua data
                return view('admin.dashboard.index', compact('stats', 'recentPendingReports', 'trendChart', 'sourceChart'));

            case 'field-officer':
                // --- Menyiapkan Data Canggih untuk Dashboard Field Officer ---
                $officerId = $user->id;

                // 1. Statistik Kinerja Personal (KPI Cards)
                $stats = [
                    'new_tasks_available'     => Report::where('status', 'verified')->count(),
                    'in_progress_by_user'     => Report::where('status', 'in_progress')
                        ->whereHas('followUp.officers', fn($q) => $q->where('users.id', $officerId))
                        ->count(),
                    'completed_by_user_month' => ReportFollowUp::whereHas('officers', fn($q) => $q->where('users.id', $officerId))
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count(),
                ];

                // 2. Data untuk Grafik Kinerja (7 Hari Terakhir)
                $performanceData = ReportFollowUp::whereHas('officers', fn($q) => $q->where('users.id', $officerId))
                    ->where('created_at', '>=', now()->subDays(6))
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get();

                $performanceChart = [
                    'labels' => $performanceData->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->isoFormat('dddd, D M')),
                    'values' => $performanceData->pluck('count'),
                ];

                // 3. Daftar Tugas Terbaru yang Siap Diambil
                $recentTasks = Report::where('status', 'verified')
                    ->with('resident')
                    ->latest('verified_at')
                    ->take(5)
                    ->get();

                // Tampilkan view khusus Field Officer dengan semua data
                return view('field.dashboard.index', compact('stats', 'recentTasks', 'performanceChart'));

            case 'leader':
                // Langsung arahkan ke controller khusus Leader untuk menjaga kerapian
                return redirect()->route('leader.dashboard');

            case 'resident':

                // 1. Ambil 3 laporan terakhir untuk ditampilkan di daftar
                $latestReports = Report::where('resident_id', $userId)
                    ->with('images')
                    ->latest()
                    ->take(5)
                    ->get();

                // 2. Ambil data statistik laporan HANYA untuk user ini
                $reportStats = [
                    'total'     => Report::where('resident_id', $userId)->count(),
                    'processed' => Report::where('resident_id', $userId)->whereIn('status', ['verified', 'in_progress'])->count(),
                    'completed' => Report::where('resident_id', $userId)->where('status', 'completed')->count(),
                    'rejected'  => Report::where('resident_id', $userId)->where('status', 'rejected')->count(),
                ];

                if ($request->has('from_creation')) {
                    // Siapkan pesan sukses manual untuk ditampilkan di view
                    session()->flash('success', 'Laporan Anda berhasil dikirim!');
                }

                // 3. Kirim kedua set data ke view
                return view('resident.dashboard', [
                    'reports' => $latestReports,
                    'stats'   => $reportStats,
                ]); // Sementara pakai view default

            default:
                return view('dashboard');
        }
    }
}
