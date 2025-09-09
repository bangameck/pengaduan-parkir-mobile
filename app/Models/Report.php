<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    /**
     * Mass assignment protection.
     * Menggunakan guarded kosong berarti semua kolom boleh diisi.
     * Alternatif dari $fillable.
     */
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'verified_at'  => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'report_code';
    }

    protected static function booted(): void
    {
        static::deleting(function (Report $report) {
            // Logika ini akan berjalan TEPAT SEBELUM sebuah laporan dihapus.

            // 1. Hapus semua file gambar/video dari storage, lalu hapus catatannya di DB.
            $report->images()->each(function ($image) {
                Storage::disk('public')->delete($image->file_path);
                if ($image->thumbnail_path) {
                    Storage::disk('public')->delete($image->thumbnail_path);
                }
                $image->delete(); // Hapus record dari tabel report_images
            });

            // 2. Hapus data tindak lanjut (jika ada).
            if ($report->followUp) {
                // Hapus dulu media/file dari tindak lanjut
                $report->followUp->media()->each(function ($media) {
                    Storage::disk('public')->delete($media->file_path);
                    if ($media->thumbnail_path) {
                        Storage::disk('public')->delete($media->thumbnail_path);
                    }
                    $media->delete(); // Hapus record dari tabel follow_up_media
                });

                // Hapus record tindak lanjut itu sendiri
                $report->followUp->delete();
            }

            // 3. Hapus semua catatan riwayat status.
            $report->statusHistories()->delete();
        });
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
