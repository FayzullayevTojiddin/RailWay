<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locomotive extends Model
{
    protected $fillable = [
        'station_id',
        'type',
        'model',
        'description'
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
