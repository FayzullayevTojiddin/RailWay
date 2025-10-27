<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'image',
        'full_name',
        'category',
        'phone_number',
        'birth_date',
        'joined_at',
        'document_type',
        'role',
        'sex',
        'details',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'joined_at' => 'datetime',
        'details' => 'array',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}