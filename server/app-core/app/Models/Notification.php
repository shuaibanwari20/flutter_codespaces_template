<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    protected $fillable = [
        'title', 'body', 'target', 'cluster_id', 'midwife_id', 'created_by',
    ];

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(Midwife::class, 'notification_reads')
            ->withPivot('read_at');
    }

    /** Is this notification visible to the given midwife? */
    public function isRelevantFor(Midwife $midwife): bool
    {
        return match ($this->target) {
            'all' => true,
            'cluster' => (int) $this->cluster_id === (int) $midwife->cluster_id,
            'midwife' => (int) $this->midwife_id === (int) $midwife->id,
            default => false,
        };
    }
}
