<?php
namespace App\Http\Controllers\Admin;

use App\Events\ReportStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReportController extends Controller
{
    /**
     * Menampilkan daftar laporan yang perlu diverifikasi.
     */
    public function index(Request $request)
    {
        // Ambil query pencarian dari URL, jika ada
        $search = $request->query('search');

        // Mulai query ke model Report
        $query = Report::query()->where('status', 'pending');

        // Jika ada input pencarian, tambahkan kondisi WHERE
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('report_code', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%');
            });
        }

        // Ambil data dengan urutan terbaru dan paginasi
        $reports = $query->with('resident')
            ->latest()
            ->paginate(10)
            ->withQueryString(); // <-- Ini penting agar paginasi tetap mengingat query pencarian

        // Kirim data ke view
        return view('admin.laporan.index', [
            'reports' => $reports,
            'search'  => $search, // Kirim juga variabel search ke view
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
}
