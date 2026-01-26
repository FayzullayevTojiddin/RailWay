<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EconomicIndicator extends Model
{
    protected $fillable = [
        'station_id',
        'title',
        'file',
        'description'
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
