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
        'images'
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

    public function rails(): HasMany
    {
        return $this->hasMany(Rail::class);
    }

    public function cadastres(): HasMany
    {
        return $this->hasMany(Cadastre::class);
    }
}