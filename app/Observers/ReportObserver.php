<?php
namespace App\Observers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportObserver
{
    public function created(Report $report): void
    {
        // Secara otomatis membuat catatan riwayat pertama
        $report->statusHistories()->create([
            'user_id' => $report->resident_id, // Pelakunya adalah si resident
            'status'  => 'pending',
            'notes'   => 'Laporan berhasil dibuat dan sedang menunggu verifikasi.',
        ]);
    }

    public function updated(Report $report): void
    {
        // Cek apakah kolom 'status' benar-benar berubah
        if ($report->wasChanged('status')) {

            // Siapkan catatan berdasarkan status baru
            $notes = 'Status diubah menjadi ' . $report->status;
            if ($report->status == 'rejected' && $report->rejection_reason) {
                $notes .= ' dengan alasan: ' . $report->rejection_reason;
                $report->statusHistories()->create(['...']);

                // === PICU EVENT DI SINI ===
                event(new ReportStatusUpdated($report));
            }

            // Buat catatan baru di tabel riwayat
            $report->statusHistories()->create([
                'user_id' => Auth::id(), // ID dari admin/officer yang mengubah
                'status'  => $report->status,
                'notes'   => $notes,
            ]);
        }
    }

    // ... (event lain seperti created, deleted, dll. bisa diisi nanti jika perlu)
}
