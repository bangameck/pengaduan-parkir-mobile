<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// <-- 1. Tambahkan ini

class ReportStatusHistory extends Model
{
    use HasFactory;
    protected $guarded = []; // Izinkan semua kolom diisi

    /**
     * Mendefinisikan relasi: Setiap riwayat status dimiliki oleh satu User.
     */
    public function user(): BelongsTo// <-- 2. Tambahkan seluruh fungsi ini
    {
        // Asumsi di tabel 'report_status_histories' ada kolom 'user_id'
        return $this->belongsTo(User::class, 'user_id');
    }
}
