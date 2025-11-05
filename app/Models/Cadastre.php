<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cadastre extends Model
{
    protected $fillable = [
        'station_id',
        'name',                 // Bino va inshootning nomji
        'cadastre_number',      // Kadastr raqami
        'floors_count',         // Qavat soni
        'construction_area',    // Qurilish osti maydoni
        'total_area',           // Umumiy maydoni
        'useful_area',          // Umumiy foydali maydoni
        'details',              // Qo'shimcha ma'lumotlar (JSON)
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * Details JSON structure:
     * - passport_number: Pasport raqami
     * - location: Manzil
     * - land_category: Yer kategoriyasi
     * - land_type: Yer turi
     * - notes: Qo'shimcha izohlar
     */
}