<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Baris ini mungkin ada, biarkan saja
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// PASTIKAN BARIS INI ADA UNTUK MENGHUBUNGKAN KE MODEL ROLE
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
}
