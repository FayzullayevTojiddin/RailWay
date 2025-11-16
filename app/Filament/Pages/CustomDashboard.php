<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use App\Models\Station;
use Illuminate\Support\Facades\Storage;

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
        $stations = Station::with(['employees', 'cadastres', 'branchRailways'])->get();
        $result = [];
        
        foreach ($stations as $station) {
            $coords = $station->coordinates ?? [];
            $details = $station->details ?? [];
            $employeesCount = $station->employees->count();
            $totalArea = $station->cadastres->sum('total_area');
            $branchRailsCount = $station->branchRailways->count();
            
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
                'images' => collect($station->images ?? [])->map(function ($image) {
                    return url('/station-images/' . $image);
                })->toArray(),
                'details' => [
                    'employees' => $employeesCount,
                    'area' => round($totalArea, 2),
                    'branch_tracks' => $branchRailsCount,
                    'facilities' => $details['facilities'] ?? [],
                    '360_link' => $details['360_link'] ?? null
                ]
            ];
        }
        
        return $result;
    }
}