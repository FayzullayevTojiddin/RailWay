<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avtomobil extends Model
{
    protected $fillable = [
        'station_id',
        'rusumi',
        'davlat_raqami',
        'ishlab_chiqarilgan_yili',
        'biriktirilgan_shaxs',
        'rasmlar',
        'texpassport_old',
        'texpassport_orqa',
    ];

    protected $casts = [
        'rasmlar' => 'array',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
