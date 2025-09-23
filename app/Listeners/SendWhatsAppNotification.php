<?php
namespace App\Listeners;

use App\Events\ReportStatusUpdated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendWhatsAppNotification
{
    /**
     * Handle the event.
     */
    public function handle(ReportStatusUpdated $event): void
    {
        $report    = $event->report;
        $resident  = $report->resident;
        $message   = '';
        $reportUrl = route('public.laporan.show', $report);

        // Sapaan dan informasi dasar
        $header = "Yth. Bapak/Ibu *" . $resident->name . "*,\n\n"
        . "Kami memberitahukan perkembangan terbaru mengenai laporan Anda dengan detail sebagai berikut:\n"
        . "🔢 *Kode Laporan:* {$report->report_code}\n"
        . "🏷️ *Judul:* " . Str::limit($report->title, 30);

        // Pesan dinamis berdasarkan status
        switch ($report->status) {
            case 'verified':
                $statusMessage = "✅ *Status: DIVERIFIKASI*\n\n"
                    . "Laporan Anda telah divalidasi oleh petugas administrasi kami dan akan segera dijadwalkan untuk ditindak lanjuti oleh tim di lapangan.";
                break;

            case 'rejected':
                $rejectionReason = $report->rejection_reason ?? 'Tidak ada alasan spesifik yang diberikan.';
                $statusMessage   = "❌ *Status: DITOLAK*\n\n"
                    . "Setelah peninjauan, dengan berat hati kami memberitahukan bahwa laporan Anda tidak dapat kami proses lebih lanjut.\n"
                    . "*Alasan:* {$rejectionReason}";
                break;

            case 'in_progress':
                $statusMessage = "⚙️ *Status: SEDANG DITINDAKLANJUTI*\n\n"
                    . "Tim petugas lapangan kami sedang dalam proses menangani laporan Anda. Perkembangan lebih lanjut akan kami informasikan kembali.";
                break;

            case 'completed':
                $statusMessage = "🎉 *Status: SELESAI DITANGANI*\n\n"
                    . "Laporan Anda telah berhasil ditindaklanjuti oleh petugas kami. Kami sangat menghargai partisipasi Anda dalam menjaga ketertiban.";
                break;

            default:
                // Jika ada status lain yang tidak ingin kita kirim notifikasi, kita hentikan di sini
                return;
        }

        // Penutup dan footer
        $footer = "Untuk melihat rincian lengkap, silakan kunjungi tautan berikut:\n"
            . $reportUrl . "\n\n"
            . "Terima kasih.\n\n"
            . "_Hormat kami,_\n"
            . "*Tim SiParkirKita*";

        // Gabungkan semua bagian pesan
        $message = implode("\n\n", [$header, $statusMessage, $footer]);

        if ($resident->phone_number) {
            try {
                Http::withHeaders(['Authorization' => config('services.fonnte.token')]) // Lebih baik memanggil config dari services
                    ->post('https://api.fonnte.com/send', [
                        'target'  => $resident->phone_number,
                        'message' => $message,
                    ]);
            } catch (\Exception $e) {
                Log::error('Gagal mengirim WhatsApp notifikasi status via Fonnte: ' . $e->getMessage());
            }
        }
    }
}
