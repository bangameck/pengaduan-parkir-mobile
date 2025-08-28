<?php
namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userRole = Auth::user()->role->name;

        if (in_array($userRole, ['resident', 'leader'])) {

            // Ambil ID user yang sedang login
            $userId = Auth::id();

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
            ]);

        }

        return view('dashboard');
    }
}
