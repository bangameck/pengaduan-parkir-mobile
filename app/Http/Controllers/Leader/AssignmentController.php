<?php
namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    /**
     * Menampilkan form untuk menugaskan petugas ke sebuah laporan.
     */
    public function create(Report $report)
    {
        // Pastikan hanya laporan yang 'pending' yang bisa diproses di sini.
        if ($report->status !== 'pending') {
            return redirect()->route('leader.team.management')
                ->with('error', 'Laporan ini tidak lagi dalam status "Pending".');
        }

        $fieldOfficers = User::whereHas('role', fn($q) => $q->where('name', 'field-officer'))->orderBy('name')->get();

        return view('leader.assignment.create', compact('report', 'fieldOfficers'));
    }

    /**
     * Memproses verifikasi, mengubah status, dan mengirim notifikasi tugas.
     */
    public function store(Request $request, Report $report)
    {
        $validated = $request->validate([
            'officer_ids'   => 'required|array|min:1',
            'officer_ids.*' => 'exists:users,id',
        ], [
            'officer_ids.required' => 'Anda harus memilih setidaknya satu petugas untuk ditugaskan.',
        ]);

        try {
            // Kita tidak lagi membuat record follow_up di sini.
            // Kita langsung UPDATE status laporan.
            $report->update([
                'status'           => 'verified',
                'admin_officer_id' => Auth::id(), // Leader yang bertindak sebagai verifikator
                'verified_at'      => now(),
            ]);

            // Buat record tindak lanjut awal & lampirkan petugas
            // Ini PENTING agar petugas tahu ini tugas mereka
            $followUp = $report->followUp()->create([
                'notes'      => 'Tugas diverifikasi dan ditetapkan oleh Pimpinan.',
                'officer_id' => $validated['officer_ids'][0], // Ambil petugas pertama sebagai penanggung jawab utama
            ]);
            $followUp->officers()->sync($validated['officer_ids']);

            // Kirim notifikasi WhatsApp ke petugas yang dipilih
            $this->sendAssignmentNotification($report, $validated['officer_ids']);

            // Picu event umum bahwa status telah berubah
            event(new ReportStatusUpdated($report->fresh()));

        } catch (\Exception $e) {
            Log::error("Gagal verifikasi & tugaskan laporan #{$report->id}: " . $e->getMessage());

            $errorMessage = app()->environment('local')
                ? 'Terjadi kesalahan: ' . $e->getMessage()
                : 'Terjadi kesalahan internal. Silakan coba lagi.';

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        return redirect()->route('leader.team.management')
            ->with('success', "Laporan #{$report->report_code} berhasil diverifikasi dan ditugaskan.");
    }

    /**
     * Mengirim notifikasi WhatsApp ke petugas yang ditugaskan.
     */
    private function sendAssignmentNotification(Report $report, array $officerIds)
    {
        // Ambil token dari cache/database SEKALI SAJA di luar loop
        $fonnteToken = config('fonnte_token');

        // Jika tidak ada token, jangan lanjutkan proses dan catat di log
        if (! $fonnteToken) {
            Log::error('Gagal mengirim notifikasi tugas: Fonnte Token tidak ditemukan di database/cache.');
            return;
        }

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
                    // Gunakan variabel token yang sudah kita siapkan
                    Http::withHeaders(['Authorization' => $fonnteToken])
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
}
