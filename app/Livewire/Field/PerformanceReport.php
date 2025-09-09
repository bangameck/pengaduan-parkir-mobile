<?php
namespace App\Livewire\Field;

use App\Models\ReportFollowUp;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PerformanceReport extends Component
{
    public $selectedMonth;
    public $months = [];
    public $year;

    public function mount()
    {
        $this->year          = now()->year;
        $this->selectedMonth = now()->format('Y-m'); // Format: 2025-09
        $this->generateMonthList();
    }

    // Fungsi untuk membuat daftar bulan yang bisa dipilih
    protected function generateMonthList()
    {
        $this->months = collect(range(1, 12))->mapWithKeys(function ($month) {
            $date = \Carbon\Carbon::createFromDate($this->year, $month, 1);
            return [$date->format('Y-m') => $date->isoFormat('MMMM YYYY')];
        })->toArray();
    }

    // Method untuk men-generate dan men-download PDF
    public function exportPdf()
    {
        $data = $this->fetchPerformanceData();

        $pdf = Pdf::loadView('field.kinerja.pdf', $data);

        // Nama file: Laporan Kinerja - Nama Petugas - September 2025.pdf
        $fileName = 'Laporan Kinerja - ' . Auth::user()->name . ' - ' . \Carbon\Carbon::parse($this->selectedMonth)->isoFormat('MMMM YYYY') . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $fileName
        );
    }

    // Method terpisah untuk mengambil data, agar bisa dipakai ulang
    protected function fetchPerformanceData()
    {
        $user           = Auth::user();
        [$year, $month] = explode('-', $this->selectedMonth);

        $followUps = ReportFollowUp::whereHas('officers', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->with('report') // Ambil data laporan terkait
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $totalReports          = $followUps->count();
        $averageCompletionTime = null; // Bisa dikembangkan nanti

        return [
            'officer'               => $user,
            'selectedPeriod'        => \Carbon\Carbon::parse($this->selectedMonth)->isoFormat('MMMM YYYY'),
            'followUps'             => $followUps,
            'totalReports'          => $totalReports,
            'averageCompletionTime' => $averageCompletionTime,
        ];
    }

    public function render()
    {
        return view('livewire.field.performance-report', $this->fetchPerformanceData())
            ->layout('layouts.app');
    }
}
