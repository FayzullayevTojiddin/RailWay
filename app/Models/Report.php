<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Station;

class Report extends Model
{
    protected $fillable = [
        'station_id',
        'type',
        'planned_value',
        'actual_value',
        'date',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'planned_value' => 'integer',
        'actual_value' => 'integer',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
