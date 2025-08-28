<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    use HasFactory;

    /**
     * Mass assignment protection.
     * Menggunakan guarded kosong berarti semua kolom boleh diisi.
     * Alternatif dari $fillable.
     */
    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'report_code';
    }

    /**
     * Relasi: Sebuah laporan dimiliki oleh satu Resident (User).
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    /**
     * Relasi: Sebuah laporan bisa memiliki banyak gambar.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ReportImage::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    public function followUp(): HasOne
    {
        return $this->hasOne(ReportFollowUp::class);
    }
}
