<?php
namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class PublicReportController extends Controller
{
    public function index(Request $request, $status = null)
    {
        $query = Report::query();

        // Hanya tampilkan status yang aman untuk publik
        $allowedStatus = ['completed', 'in_progress', 'verified', 'rejected'];

        if ($status && in_array($status, $allowedStatus)) {
            $query->where('status', $status);
        } else {
            // Jika status tidak valid atau null (untuk 'Total Laporan'), tampilkan semua yang aman
            $query->whereIn('status', $allowedStatus);
        }

        $reports = $query->with('resident', 'images', 'followUp')
            ->latest('updated_at')
            ->paginate(10);

        // 👉 transform() DIHAPUS, gak perlu lagi

        return view('laporan-publik', [
            'reports' => $reports,
            'status'  => $status,
        ]);
    }

    public function show(Report $report)
    {
        // Pastikan kita hanya menampilkan laporan yang statusnya boleh dilihat publik
        $allowedStatus = ['pending', 'verified', 'in_progress', 'completed', 'rejected'];
        if (! in_array($report->status, $allowedStatus)) {
            abort(404);
        }

        // Load relasi yang dibutuhkan agar query efisien
        $report->load('images', 'statusHistories.user', 'statusHistories.report', 'followUp.officers');

        // 👉 Tidak perlu set report_name manual, karena sudah otomatis lewat accessor

        return view('laporan-publik-show', [
            'report'        => $report,
            'reportForMeta' => $report,
            'title'         => 'Detail Laporan #' . $report->report_code,
        ]);
    }
}
