<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpMedia extends Model
{
    protected $guarded = [];

    public function followUp(): BelongsTo
    {
        return $this->belongsTo(ReportFollowUp::class, 'report_follow_up_id');
        // foreign key = follow_up_id (sesuaikan dengan kolom di tabel follow_up_media)
    }
}
