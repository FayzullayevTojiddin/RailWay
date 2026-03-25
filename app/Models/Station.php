<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'coordinates',
        'title',
        'description',
        'details',
        'images',
        'ai_response'
    ];

    protected $casts = [
        'details' => 'array',
        'images' => 'array',
        'coordinates' => 'array',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function cadastres(): HasMany
    {
        return $this->hasMany(Cadastre::class);
    }

    public function branchRailways(): HasMany
    {
        return $this->hasMany(BranchRailway::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function locomotives(): HasMany
    {
        return $this->hasMany(Locomotive::class);
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(EconomicIndicator::class);
    }

    public function avtomobillar(): HasMany
    {
        return $this->hasMany(Avtomobil::class);
    }

    public function mikrosxemalar(): HasMany
    {
        return $this->hasMany(Mikrosxema::class);
    }
}