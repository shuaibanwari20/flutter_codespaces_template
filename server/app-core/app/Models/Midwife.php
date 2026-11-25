<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Midwife extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'code', 'full_name', 'phone', 'cluster_id', 'pin_hash',
        'device_id', 'leave_balance', 'active',
    ];

    protected $hidden = ['pin_hash'];

    protected $casts = [
        'leave_balance' => 'integer',
        'active' => 'boolean',
    ];

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function problemReports(): HasMany
    {
        return $this->hasMany(ProblemReport::class);
    }
}
