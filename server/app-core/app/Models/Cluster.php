<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cluster extends Model
{
    protected $fillable = [
        'code', 'name', 'village', 'district', 'province',
        'lat', 'lng', 'radius_m', 'active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'radius_m' => 'integer',
        'active' => 'boolean',
    ];

    public function midwives(): HasMany
    {
        return $this->hasMany(Midwife::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /** Display label: "کلستر 12 — قریه نوآباد" */
    public function displayLabel(): string
    {
        return $this->name.' — '.$this->village;
    }
}
