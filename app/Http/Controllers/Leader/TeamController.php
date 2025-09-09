<?php
namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Report; // <-- Import Role
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function show(User $user)
    {
        $user->load('role');

        // Inisialisasi variabel dengan nilai default untuk MENCEGAH SEMUA ERROR
        $relatedReports = collect();
        $chartData      = ['labels' => [], 'values' => []];
        $reportCounts   = ['today' => 0, 'this_month' => 0, 'this_year' => 0, 'total' => 0];

        // Ambil data spesifik hanya jika role-nya relevan
        if (in_array($user->role->name, ['resident', 'admin-officer', 'field-officer'])) {
            $query = null;

            switch ($user->role->name) {
                case 'resident':
                    $query = $user->reports();
                    break;

                case 'admin-officer':
                    $query = Report::where('admin_officer_id', $user->id);
                    break;

                case 'field-officer':
                    // ==========================================================
                    // == PERBAIKAN FINAL ADA DI SINI ==
                    // ==========================================================
                    // Cari laporan yang punya relasi 'followUp',
                    // DAN di dalam relasi 'followUp' itu, cari relasi 'officers'
                    // yang ID-nya cocok dengan ID field officer ini.
                    $query = Report::whereHas('followUp.officers', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
                    break;
            }

            if ($query && $query->exists()) {
                $relatedReports = $query->clone()->with('images')->latest()->paginate(5, ['*'], 'reportsPage');
                $reportCounts   = $this->getReportCounts($query);
                $chartData      = $this->getChartData($query);
            }
        }

        return view('leader.users.show', compact(
            'user',
            'relatedReports',
            'chartData',
            'reportCounts'
        ));
    }

    private function getReportCounts($query)
    {
        return [
            'today'      => $query->clone()->whereDate('created_at', today())->count(),
            'this_month' => $query->clone()->whereMonth('created_at', today()->month)->whereYear('created_at', today()->year)->count(),
            'this_year'  => $query->clone()->whereYear('created_at', today()->year)->count(),
            'total'      => $query->clone()->count(),
        ];
    }

    /**
     * Helper function untuk menyiapkan data grafik.
     */
    private function getChartData($query)
    {
        $data = $query->clone()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('d M');
            }),
            'values' => $data->pluck('count'),
        ];
    }
}
