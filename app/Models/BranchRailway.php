<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchRailway extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'name',
        'stir',
        'image',
        'length',
        'established_year',
        'cancelled_at',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'cancelled_at' => 'date',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
