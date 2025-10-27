<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class EmployeesGenderChartWidget extends ChartWidget
{
    protected ?string $heading = 'Xodimlar taqsimoti';
    
    public ?Model $record = null;

    protected ?string $maxHeight = '250px';
    
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [
                    [
                        'label' => 'Xodimlar soni',
                        'data' => [0, 0],
                        'backgroundColor' => ['#3b82f6', '#ec4899'],
                    ],
                ],
                'labels' => ['Erkaklar', 'Ayollar'],
            ];
        }

        $employees = $this->record->employees;
        $males = $employees->where('sex', 'male')->count();
        $females = $employees->where('sex', 'female')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Xodimlar soni',
                    'data' => [$males, $females],
                    'backgroundColor' => ['#3b82f6', '#ec4899'],
                ],
            ],
            'labels' => ['Erkaklar', 'Ayollar'],
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