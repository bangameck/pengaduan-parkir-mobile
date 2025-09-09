<?php
namespace App\Listeners;

use App\Events\ReportStatusUpdated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification
{
    public function handle(ReportStatusUpdated $event): void
    {
        $report   = $event->report;
        $resident = $report->resident; // Asumsi relasi 'resident' sudah ada di model Report
        $message  = '';

        switch ($report->status) {
            case 'verified':
                $message = "✅ Laporan Anda (Kode: *{$report->report_code}*) telah *DIVERIFIKASI*.\n\nPetugas kami akan segera menindaklanjuti. Terima kasih.";
                break;
            case 'rejected':
                $message = "❌ Laporan Anda (Kode: *{$report->report_code}*) *DITOLAK*.\n\nAlasan: {$report->rejection_reason}\n\nTerima kasih.";
                break;
            case 'completed':
                $message = "🎉 Laporan Anda (Kode: *{$report->report_code}*) telah *SELESAI* ditindaklanjuti.\n\nTerima kasih atas partisipasi Anda dalam menjaga ketertiban.";
                break;
        }

        if ($message && $resident->phone_number) {
            try {
                Http::withHeaders(['Authorization' => config('fonnte_token')])
                    ->post('https://api.fonnte.com/send', [
                        'target'  => $resident->phone_number,
                        'message' => $message,
                    ]);
            } catch (\Exception $e) {
                Log::error('Gagal mengirim WhatsApp via Fonnte: ' . $e->getMessage());
            }
        }
    }
}
