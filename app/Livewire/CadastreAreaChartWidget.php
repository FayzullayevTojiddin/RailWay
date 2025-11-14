<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class CadastreAreaChartWidget extends ChartWidget
{
    protected ?string $heading = 'Kadastr maydonlari';

    public ?Model $record = null;

    protected ?string $maxHeight = '250px';
    
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [
                    [
                        'data' => [0, 0, 0],
                        'backgroundColor' => ['#3b82f6', '#f59e0b', '#10b981'],
                    ],
                ],
                'labels' => ['Qurilish osti', 'Umumiy maydon', 'Foydali maydon'],
            ];
        }

        $cadastres = $this->record->cadastres;
        
        $totalConstructionArea = 0;
        $totalArea = 0;
        $totalUsefulArea = 0;

        foreach ($cadastres as $cadastre) {
            $totalConstructionArea += (float) ($cadastre->construction_area ?? 0);
            $totalArea += (float) ($cadastre->total_area ?? 0);
            $totalUsefulArea += (float) ($cadastre->useful_area ?? 0);
        }

        return [
            'datasets' => [
                [
                    'data' => [
                        round($totalConstructionArea, 2),
                        round($totalArea, 2),
                        round($totalUsefulArea, 2)
                    ],
                    'backgroundColor' => ['#3b82f6', '#f59e0b', '#10b981'],
                ],
            ],
            'labels' => [
                'Qurilish osti (' . round($totalConstructionArea, 2) . ' kv.m)',
                'Umumiy maydon (' . round($totalArea, 2) . ' kv.m)',
                'Foydali maydon (' . round($totalUsefulArea, 2) . ' kv.m)'
            ],
        ];
    }
    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}