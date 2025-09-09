<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Baris ini mungkin ada, biarkan saja
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// PASTIKAN BARIS INI ADA UNTUK MENGHUBUNGKAN KE MODEL ROLE
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable// Mungkin ada 'implements MustVerifyEmail', biarkan saja

{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username', // <-- Tambahkan ini
        'email',
        'password',
        'phone_number',
        'image', // <-- Tambahkan ini
        'role_id',
        'otp_code',
        'otp_expires_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Get the role associated with the user.
     *
     * INI BAGIAN TERPENTING, PASTIKAN SEPERTI INI
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'resident_id');
    }

    /**
     * Laporan yang diverifikasi oleh user ini (sebagai admin officer).
     */
    public function verifiedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'admin_officer_id');
    }

    /**
     * Tindak lanjut yang dilakukan oleh user ini (sebagai field officer).
     */
    public function followUps(): BelongsToMany
    {
        return $this->belongsToMany(ReportFollowUp::class, 'follow_up_officer');
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            // Logika ini akan berjalan TEPAT SEBELUM user dihapus

            // 1. Jika user adalah resident, hapus semua laporannya.
            // Menghapus laporan juga akan memicu Observer-nya untuk menghapus data terkait lainnya (gambar, history, dll).
            if ($user->role->name === 'resident') {
                $user->reports()->each(function ($report) {
                    $report->delete(); // Memanggil delete() pada setiap laporan
                });
            }

            // 2. Jika user adalah admin_officer, null-kan ID-nya di laporan yang pernah ia verifikasi.
            // Kita tidak menghapus laporannya, hanya melepas keterkaitannya.
            if ($user->role->name === 'admin-officer') {
                $user->verifiedReports()->update(['admin_officer_id' => null]);
            }

            // 3. Jika user adalah field_officer, null-kan ID-nya di tindak lanjut yang pernah ia buat.
            if ($user->role->name === 'field-officer') {
                $user->followUps()->update(['field_officer_id' => null]);
            }

            // 4. Hapus file gambar profilnya jika ada.
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
        });
    }
}
