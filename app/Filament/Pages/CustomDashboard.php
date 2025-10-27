<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use App\Models\Station;

class CustomDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';
    
    protected static ?string $navigationLabel = 'Xarita';
    
    protected static ?int $navigationSort = -2;
    
    protected static ?string $slug = 'dashboard';
    
    protected string $view = 'filament.pages.custom-dashboard';
    
    protected static string $layout = 'filament-panels::components.layout.base';
    
    public function getStations()
    {
        return Station::all(['id', 'title', 'type', 'coordinates'])->map(function ($station) {
            return [
                'id' => $station->id,
                'title' => $station->title,
                'type' => $station->type,
                'coordinates' => $station->coordinates ?? ['x' => 50, 'y' => 50]
            ];
        })->toArray();
    }
}