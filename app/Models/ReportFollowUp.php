<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportFollowUp extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
    public function media(): HasMany
    {
        return $this->hasMany(FollowUpMedia::class);
    }

    public function officers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follow_up_officer');
    }

    public function user(): BelongsTo
    {
        // Asumsi di tabel 'report_follow_ups' ada kolom 'user_id'
        return $this->belongsTo(User::class, 'user_id');
    }

    public function report(): BelongsTo
    {
        // Asumsi di tabel 'report_follow_ups' ada kolom 'report_id'
        return $this->belongsTo(Report::class, 'report_id');
    }
}
