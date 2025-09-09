<?php
namespace App\Livewire\Leader;

use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ReportAssignment extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedReport;
    public $selectedOfficers = [];
    public $showAssignModal  = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openAssignModal(Report $report)
    {
        $this->selectedReport   = $report;
        $this->selectedOfficers = [];
        $this->showAssignModal  = true;
    }

    public function assignOfficers()
    {
        $this->validate([
            'selectedOfficers'   => 'required|array|min:1',
            'selectedOfficers.*' => 'exists:users,id',
        ], [
            'selectedOfficers.required' => 'Anda harus memilih setidaknya satu petugas.',
        ]);

        try {
            DB::transaction(function () {
                // 1. Buat atau dapatkan record tindak lanjut
                $followUp = $this->selectedReport->followUp()->firstOrCreate([
                    'notes' => 'Tugas telah ditetapkan oleh Pimpinan.',
                ]);

                // 2. Lampirkan tim petugas ke tindak lanjut
                $followUp->officers()->sync($this->selectedOfficers);

                // 3. Update status laporan utama
                $this->selectedReport->update(['status' => 'in_progress']);
            });

            // 4. Kirim notifikasi WhatsApp ke petugas yang dipilih
            $this->sendAssignmentNotification($this->selectedReport, $this->selectedOfficers);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan penugasan. Error: ' . $e->getMessage());
            $this->showAssignModal = false;
            return;
        }

        $this->showAssignModal = false;
        session()->flash('success', 'Tugas berhasil ditugaskan ke petugas terpilih.');
    }

    private function sendAssignmentNotification(Report $report, array $officerIds)
    {
        $officers  = User::find($officerIds);
        $reportUrl = route('petugas.tugas.createFollowUp', $report);
        $message   = "🔔 *Tugas Baru untuk Anda!*\n\n"
            . "Anda telah ditugaskan oleh Pimpinan untuk menangani laporan:\n\n"
            . "*Kode:* `{$report->report_code}`\n"
            . "*Judul:* {$report->title}\n\n"
            . "Segera lihat detail dan lakukan tindak lanjut melalui link berikut:\n"
            . $reportUrl;

        foreach ($officers as $officer) {
            if ($officer->phone_number) {
                try {
                    Http::withHeaders(['Authorization' => config('fonnte_token')]) // Mengambil dari DB/Cache
                        ->post('https://api.fonnte.com/send', [
                            'target'  => $officer->phone_number,
                            'message' => $message,
                        ]);
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim notifikasi tugas ke {$officer->name}: " . $e->getMessage());
                }
            }
        }
    }

    public function render()
    {
        $query = Report::where('status', 'pending');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('report_code', 'like', '%' . $this->search . '%')
                    ->orWhere('title', 'like', '%' . $this->search . '%')
                    ->orWhere('location_address', 'like', '%' . $this->search . '%');
            });
        }

        $reports = $query->with('resident', 'images')->latest('verified_at')->paginate(10);

        $fieldOfficers = User::whereHas('role', fn($q) => $q->where('name', 'field-officer'))->get();

        return view('livewire.leader.report-assignment', [
            'reports'       => $reports,
            'fieldOfficers' => $fieldOfficers,
        ])->layout('layouts.app');
    }
}
