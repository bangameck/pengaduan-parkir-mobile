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
        // Hanya jalankan jika kolom 'status' benar-benar berubah. Ini sudah benar.
        if ($report->wasChanged('status')) {

                                                                                    // 1. Siapkan dulu pesan 'notes' yang deskriptif berdasarkan status baru.
            $notes = 'Status diperbarui menjadi ' . ucfirst($report->status) . '.'; // ucfirst() membuat huruf pertama besar

            if ($report->status == 'rejected' && ! empty($report->rejection_reason)) {
                $notes = 'Laporan ditolak dengan alasan: ' . $report->rejection_reason;
            } elseif ($report->status == 'verified') {
                $notes = 'Laporan telah diverifikasi dan diteruskan ke petugas lapangan.';
            } elseif ($report->status == 'in_progress') {
                $notes = 'Laporan sedang dalam proses tindak lanjut oleh petugas.';
            } elseif ($report->status == 'completed') {
                $notes = 'Laporan telah selesai ditindaklanjuti.';
            }

            // 2. Buat catatan riwayat di database HANYA SATU KALI dengan data yang sudah disiapkan.
            $report->statusHistories()->create([
                'user_id' => auth()->id(), // ID dari admin/officer yang mengubah
                'status'  => $report->status,
                'notes'   => $notes,
            ]);

            // 3. JANGAN panggil event() di sini.
            //    Biarkan Controller yang bertanggung jawab untuk memicu notifikasi keluar (seperti WhatsApp).
            //    Ini mencegah logika ganda dan membuat alur lebih jelas.
            //    event(new ReportStatusUpdated($report)); // <-- BARIS INI KITA HAPUS DARI OBSERVER
        }
    }

    // ... (event lain seperti created, deleted, dll. bisa diisi nanti jika perlu)
}
