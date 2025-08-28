<?php
namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama publik.
     */
    public function index(): View
    {
        // Cek apakah ada user yang sedang login dan rolenya adalah resident atau leader
        if (Auth::check() && in_array(Auth::user()->role->name, ['resident', 'leader'])) {

            // --- LOGIKA UNTUK USER YANG SUDAH LOGIN ---

            // Ambil statistik laporan MILIK PRIBADI user ini
            // 1. Mengambil Data Statistik
            $stats = [
                'total'       => Report::count(),
                'pending'     => Report::where('status', 'pending')->count(),
                'verified'    => Report::where('status', 'verified')->count(),
                'in_progress' => Report::where('status', 'in_progress')->count(),
                'completed'   => Report::where('status', 'completed')->count(),
                'rejected'    => Report::where('status', 'rejected')->count(),
            ];

            // 2. Mengambil Daftar Laporan yang Sudah Selesai untuk Ditampilkan Publik
            $completedReports = Report::where('status', 'completed')
                ->with('resident', 'images', 'followUp') // Ambil semua data terkait
                ->latest('completed_at')                 // Urutkan dari yang paling baru selesai
                ->paginate(5);                           // Tampilkan 5 laporan per halaman

            // Tampilkan view khusus untuk resident yang sudah login
            return view('home-resident', [
                'stats'   => $stats,
                'reports' => $completedReports,
            ]);

        }

        $stats = [
            'total'       => Report::count(),
            'pending'     => Report::where('status', 'pending')->count(),
            'verified'    => Report::where('status', 'verified')->count(),
            'in_progress' => Report::where('status', 'in_progress')->count(),
            'completed'   => Report::where('status', 'completed')->count(),
            'rejected'    => Report::where('status', 'rejected')->count(),
        ];

        // 2. Mengambil Daftar Laporan yang Sudah Selesai untuk Ditampilkan Publik
        $completedReports = Report::where('status', 'completed')
            ->with('resident', 'images', 'followUp') // Ambil semua data terkait
            ->latest('completed_at')                 // Urutkan dari yang paling baru selesai
            ->paginate(5);                           // Tampilkan 5 laporan per halaman

        // Tampilkan view khusus untuk resident yang sudah login
        return view('welcome', [
            'stats'   => $stats,
            'reports' => $completedReports,
        ]);

    }
}
