<?php
namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportFollowUp;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $teamMembersQuery = User::whereHas('role', function ($query) {
            $query->where('name', 'field-officer');
        });

        if ($search) {
            $teamMembersQuery->where('name', 'like', '%' . $search . '%');
        }

        $teamMembers = $teamMembersQuery->withCount([
            'followUps as total_completed_reports',
            'followUps as month_completed_reports' => function ($query) {
                $query->whereMonth('report_follow_ups.created_at', now()->month)
                    ->whereYear('report_follow_ups.created_at', now()->year);
            },
        ])->orderByDesc('total_completed_reports') // <-- DIURUTKAN BERDASARKAN JUMLAH LAPORAN (BARU)
            ->get();

        $stats = [
            'total_officers'       => $teamMembers->count(),
            'completed_this_month' => Report::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
            'in_progress_now'      => Report::where('status', 'in_progress')->count(),
        ];

        $chartData = [
            'labels' => $teamMembers->pluck('name'),
            'values' => $teamMembers->pluck('total_completed_reports'),
        ];

        $mapLocations = ReportFollowUp::with('report:id,title,report_code')
            ->whereNotNull(['proof_latitude', 'proof_longitude'])
            ->latest()
            ->take(50)
            ->get();

        $recentReports = Report::with('resident', 'images')
            ->latest()
            ->take(5)
            ->get();

        return view('leader.dashboard.index', compact(
            'teamMembers',
            'stats',
            'chartData',
            'mapLocations',
            'search',
            'recentReports'
        ));
    }
}
