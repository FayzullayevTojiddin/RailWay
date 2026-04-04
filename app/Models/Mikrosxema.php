<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mikrosxema extends Model
{
    protected $fillable = [
        'station_id',
        'nomi',
        'texnik_holati',
        'biriktirilgan_joyi',
        'biriktirilgan_shaxs',
        'rasmlar',
    ];

    protected $casts = [
        'rasmlar' => 'array',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
