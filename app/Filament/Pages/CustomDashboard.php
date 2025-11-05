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
        $stations = Station::all();
        $result = [];
        
        foreach ($stations as $station) {
            $coords = $station->coordinates ?? [];
            $details = $station->details ?? [];
            
            $result[] = [
                'id' => $station->id,
                'title' => $station->title,
                'type' => $station->type,
                'coordinates' => [
                    'x' => $coords['x'] ?? 50,
                    'y' => $coords['y'] ?? 50
                ],
                'location' => [
                    'lat' => $coords['lat'] ?? null,
                    'lng' => $coords['lng'] ?? null
                ],
                'description' => $station->description ?? '',
                'images' => $station->images ?? [],
                'details' => [
                    'employees' => $details['employees'] ?? 0,
                    'area' => $details['area'] ?? 0,
                    'branch_tracks' => $details['branch_tracks'] ?? 0,
                    'railway_tracks' => $details['railway_tracks'] ?? 0,
                    'facilities' => $details['facilities'] ?? [],
                    '360_link' => $details['360_link'] ?? null
                ]
            ];
        }
        
        return $result;
    }
}