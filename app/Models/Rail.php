<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rail extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'serial_number',
        'image',
        'owner_details',
        'wagon_distance',
        'length',
        'license',
        'technical_passport',
        'direction',
        'loading_joy',
        'work_times',
        'front',
        'wagon_counts',
        'cancelled_at',
    ];

    protected $casts = [
        'owner_details' => 'array',
        'wagon_distance' => 'array',
        'license' => 'array',
        'work_times' => 'array',
        'cancelled_at' => 'datetime',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}