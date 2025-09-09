<?php
namespace App\Livewire\AdminOfficer;

use App\Exports\ReportsExport;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ReportRecap extends Component
{
    use WithPagination;

    #[Url( as : 'filter')]
    public $filterType = 'this_month';
    #[Url]
    public $startDate;
    #[Url]
    public $endDate;

    public function mount()
    {
        $this->startDate = $this->startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = $this->endDate ?? now()->endOfMonth()->format('Y-m-d');
    }

    // Fungsi untuk membangun query dasar, agar bisa dipakai ulang
    private function buildReportQuery()
    {
        $query = Report::query();

        if ($this->filterType === 'this_month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($this->filterType === 'this_year') {
            $query->whereYear('created_at', now()->year);
        } elseif ($this->filterType === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()]);
        }

        return $query;
    }

    // Fungsi untuk mendapatkan label periode
    private function getPeriodLabel()
    {
        if ($this->filterType === 'this_month') {
            return now()->isoFormat('MMMM YYYY');
        }

        if ($this->filterType === 'this_year') {
            return now()->year;
        }

        if ($this->filterType === 'custom') {
            return Carbon::parse($this->startDate)->isoFormat('D MMM Y') . ' - ' . Carbon::parse($this->endDate)->isoFormat('D MMM Y');
        }

        return 'Semua Waktu';
    }

    // FUNGSI EKSPOR
    public function exportExcel()
    {
        $reports  = $this->buildReportQuery()->with('resident')->latest()->get();
        $period   = $this->getPeriodLabel();
        $fileName = 'Rekap Laporan - ' . $period . '.xlsx';

        return Excel::download(new ReportsExport($reports, $period), $fileName);
    }

    public function exportPdf()
    {
        $reports = $this->buildReportQuery()->with('resident')->latest()->get();
        $period  = $this->getPeriodLabel();

        $pdf = Pdf::loadView('admin.laporan.rekap-laporan', [
            'reports' => $reports,
            'period'  => $period,
        ]);

        $fileName = 'Rekap Laporan - ' . $period . '.pdf';
        return response()->streamDownload(fn() => print($pdf->output()), $fileName);
    }

    public function render()
    {
        $baseQuery = $this->buildReportQuery();

        // STATISTIK UTAMA
        $stats = [
            'total'       => $baseQuery->clone()->count(),
            'completed'   => $baseQuery->clone()->where('status', 'completed')->count(),
            'rejected'    => $baseQuery->clone()->where('status', 'rejected')->count(),
            'in_progress' => $baseQuery->clone()->whereIn('status', ['verified', 'in_progress'])->count(),
            'pending'     => $baseQuery->clone()->where('status', 'pending')->count(),
        ];

        // DATA UNTUK GRAFIK TREN
        $trendData = $baseQuery->clone()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // DATA UNTUK GRAFIK PIE
        $pieChartData = [
            'labels' => ['Selesai', 'Ditolak', 'Dalam Proses', 'Pending'],
            'values' => [
                $stats['completed'],
                $stats['rejected'],
                $stats['in_progress'],
                $stats['pending'],
            ],
        ];

        // Data untuk tabel detail
        $reports = $baseQuery->clone()->with('resident')->latest()->paginate(10);

        // Kirim event untuk update chart di frontend
        $this->dispatch('updateCharts', [
            'trendData'    => [
                'labels' => $trendData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M')),
                'values' => $trendData->pluck('count'),
            ],
            'pieChartData' => $pieChartData,
        ]);

        return view('livewire.admin-officer.report-recap', [
            'stats'        => $stats,
            'reports'      => $reports,
            'pieChartData' => $pieChartData,
        ])->layout('layouts.app');
    }
}
