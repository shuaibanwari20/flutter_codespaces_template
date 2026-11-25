<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemReport extends Model
{
    protected $fillable = [
        'midwife_id', 'type', 'message', 'status', 'resolution_note',
    ];

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class);
    }
}
