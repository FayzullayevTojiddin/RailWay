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
        'name',              // Firma nomi
        'stir',              // STIR raqami
        'image',             // Rasm
        'length',            // Uzunligi (m)
        'established_year',  // Tashkil etilgan yil
        'cancelled_at',      // Bekor qilgan sana
        'details',           // Qo'shimcha ma'lumotlar (JSON)
    ];

    protected $casts = [
        'details' => 'array',
        'cancelled_at' => 'date',
    ];

    /**
     * Details JSON structure:
     * - owner_info: ['full_name' => string, 'email' => string, 'phone' => string]
     * - wagon_distances: array of distances
     * - contracts: array of contracts
     * - technical_passport_year: year
     * - directive_year: year
     * - connection_point: string
     * - cargo_work_periods: array of periods
     * - wagons_count: integer
     */

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
