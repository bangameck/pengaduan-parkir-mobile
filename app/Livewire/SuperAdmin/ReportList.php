<?php
namespace App\Livewire\SuperAdmin;

use App\Models\Report;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ReportList extends Component
{
    #[Url( as : 'q')] // Membuat pencarian bisa di-bookmark
    public string $search = '';

    #[Url] // Membuat status filter bisa di-bookmark
    public string $status = 'all';

    public int $perPage = 8; // Tampilkan 8 kartu per muatan

    #[On('report-deleted')]
    public function onReportDeleted()
    {
        // Cukup re-render komponen untuk memperbarui daftar
        // Pesan sukses akan ditampilkan oleh SweetAlert di frontend
    }

    public function loadMore()
    {
        $this->perPage += 4;
    }

    #[On('delete-report')]
    public function deleteReport($reportId)
    {
        // Otorisasi: Cek apakah user punya izin dari Gate yang kita buat
        if (Gate::denies('delete-reports')) {
            $this->dispatch('delete-failed', message: 'Anda tidak memiliki izin untuk melakukan aksi ini.');
            return;
        }

        $report = Report::find($reportId);
        if (! $report) {
            $this->dispatch('delete-failed', message: 'Laporan tidak ditemukan.');
            return;
        }

        $reportCode = $report->report_code;
        $report->delete(); // <-- Memanggil 'alat tempur' (cascade delete) yang sudah kita buat

        $this->dispatch('report-deleted-success', message: "Laporan #{$reportCode} dan semua data terkaitnya berhasil dihapus permanen.");
    }

    public function render()
    {
        $query = Report::query()->with('resident', 'images');

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('report_code', 'like', '%' . $this->search . '%')
                    ->orWhere('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('resident', function ($subq) {
                        $subq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $totalReportCount = $query->clone()->count();
        $reports          = $query->latest()->take($this->perPage)->get();

        return view('livewire.super-admin.report-list', [
            'reports'      => $reports,
            'hasMorePages' => $this->perPage < $totalReportCount,
        ])->layout('layouts.app');
    }
}
