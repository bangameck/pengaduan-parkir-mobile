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
        $allowedStatus = ['completed', 'in_progress', 'verified'];

        if ($status && in_array($status, $allowedStatus)) {
            $query->where('status', $status);
        } else {
            // Jika status tidak valid atau null (untuk 'Total Laporan'), tampilkan semua yang aman
            $query->whereIn('status', $allowedStatus);
        }

        $reports = $query->with('resident', 'images', 'followUp')
            ->latest('updated_at')
            ->paginate(10);

        return view('laporan-publik', [
            'reports' => $reports,
            'status'  => $status,
        ]);
    }
}
