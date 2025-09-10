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

    /**
     * Query dasar (belum dipaginate).
     */
    private function getBaseQuery()
    {
        $query = Report::query();

        if ($this->filterType === 'this_month') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        } elseif ($this->filterType === 'this_year') {
            $query->whereYear('created_at', now()->year);
        } elseif ($this->filterType === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween(
                'created_at',
                [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()]
            );
        }

        return $query->with('resident');
    }

    /**
     * Label periode untuk judul laporan.
     */
    private function getPeriodLabel()
    {
        if ($this->filterType === 'this_month') {
            return now()->isoFormat('MMMM YYYY');
        }

        if ($this->filterType === 'this_year') {
            return now()->year;
        }

        if ($this->filterType === 'custom') {
            return Carbon::parse($this->startDate)->isoFormat('D MMM Y')
            . ' - ' . Carbon::parse($this->endDate)->isoFormat('D MMM Y');
        }

        return 'Semua Waktu';
    }

    /**
     * Export ke Excel.
     */
    public function exportExcel()
    {
        $reports  = $this->getBaseQuery()->latest()->get();
        $period   = $this->getPeriodLabel();
        $fileName = 'Rekap Laporan - ' . $period . '.xlsx';

        return Excel::download(new ReportsExport($reports, $period), $fileName);
    }

    /**
     * Export ke PDF.
     */
    public function exportPdf()
    {
        $reports = $this->getBaseQuery()->latest()->get();
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
        $baseQuery = $this->getBaseQuery();

        // Statistik utama
        $stats = [
            'total'       => (clone $baseQuery)->count(),
            'completed'   => (clone $baseQuery)->where('status', 'completed')->count(),
            'rejected'    => (clone $baseQuery)->where('status', 'rejected')->count(),
            'in_progress' => (clone $baseQuery)->whereIn('status', ['verified', 'in_progress'])->count(),
            'pending'     => (clone $baseQuery)->where('status', 'pending')->count(),
        ];

        // Data tren
        $trendData = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Data pie chart
        $pieChartData = [
            'labels' => ['Selesai', 'Ditolak', 'Dalam Proses', 'Pending'],
            'values' => [
                $stats['completed'],
                $stats['rejected'],
                $stats['in_progress'],
                $stats['pending'],
            ],
        ];

        // Data tabel (paginate)
        $reports = (clone $baseQuery)->latest()->paginate(10);

        $reports->getCollection()->transform(function ($report) {
            $socials = ['instagram', 'tiktok', 'facebook'];

            if (in_array(strtolower($report->source), $socials)) {
                $report->reportName = $report->source_contact;
            } else {
                $report->reportName = $report->resident?->name;
            }

            return $report;
        });

        // Dispatch ke frontend untuk update chart
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
