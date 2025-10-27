<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cadastre extends Model
{
    protected $fillable = [
        'station_id',
        'image',
        'details',
        'items',
    ];

    protected $casts = [
        'details' => 'array',
        'items' => 'array',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    // Details structure:
    // {
    //     "general": {
    //         "cadastre_date": "6-Fev, 2025",
    //         "passport_number": "P-OLQT31809912",
    //         "cadastre_number": "15:07:41:02:02:0144",
    //         "location": "Farg'ona viloyati, Oltiariq tumani, Tinchlik MFY,Tinchlik ko'chasi, 155/1-uy"
    //     },
    //     "land_info": {
    //         "object_name": "Oltiariq Temir yo'l stansiyasi bino va inshootlari",
    //         "land_category": "Sanoat, transport, aloqa, mudofaa va boshqa maqsadlarga mo'ljalangan yerlar",
    //         "land_type": "Temir yo'llar yerlari",
    //         "small_category": "Temir yo'l transporti obyektlari (bundan temiryol yo'llari mustasno)",
    //         "usage_type": "Temir yo'llar yerlari",
    //         "transport_objects": "-",
    //         "service_fee": "-"
    //     },
    //     "area_info": {
    //         "total": "158,850",
    //         "paid": "158,850 kv.m",
    //         "unpaid": "220 kv.m"
    //     }
    // }

    // Items structure:
    // [
    //     {
    //         "name": "Dam olish uyi",
    //         "cadastre_code": "15:07:41:02:02:00687",
    //         "quantity": 1,
    //         "construction_area": "65.73 kv.m",
    //         "total_area": "65.73 kv.m",
    //         "useful_area": "53.20 kv.m"
    //     },
    //     {
    //         "name": "Transformator nimstansiyasi",
    //         "cadastre_code": "15:07:41:02:02:00687",
    //         "quantity": 1,
    //         "construction_area": "45.73 kv.m",
    //         "total_area": "62.73 kv.m",
    //         "useful_area": "57.20 kv.m"
    //     }
    // ]
}