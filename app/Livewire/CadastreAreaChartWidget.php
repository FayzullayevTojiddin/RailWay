<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class CadastreAreaChartWidget extends ChartWidget
{
    protected ?string $heading = 'Yer maydoni';

    public ?Model $record = null;

    protected ?string $maxHeight = '250px';
    
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [
                    [
                        'data' => [0, 0],
                        'backgroundColor' => ['#f59e0b', '#ec4899'],
                    ],
                ],
                'labels' => ['Umumiy osti maydoni', 'Umumiy foydali maydoni'],
            ];
        }

        $cadastres = $this->record->cadastres;
        
        $totalArea = 0;
        $totalUsefulArea = 0;

        foreach ($cadastres as $cadastre) {
            $items = $cadastre->items ?? [];
            
            foreach ($items as $item) {
                $area = $item['total_area'] ?? 0;
                if (is_string($area)) {
                    $area = (float) preg_replace('/[^0-9.]/', '', $area);
                }
                
                $usefulArea = $item['useful_area'] ?? 0;
                if (is_string($usefulArea)) {
                    $usefulArea = (float) preg_replace('/[^0-9.]/', '', $usefulArea);
                }
                
                $totalArea += $area;
                $totalUsefulArea += $usefulArea;
            }
        }

        return [
            'datasets' => [
                [
                    'data' => [$totalArea, $totalUsefulArea],
                    'backgroundColor' => ['#f59e0b', '#ec4899'],
                ],
            ],
            'labels' => ['Umumiy osti maydoni kv-da', 'Umumiy foydali maydoni kv-da'],
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