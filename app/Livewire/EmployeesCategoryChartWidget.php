<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class EmployeesCategoryChartWidget extends ChartWidget
{
    protected ?string $heading = 'Xodimlarning ma\'lumoti';
    
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
                        'data' => [0, 0, 0],
                        'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b'],
                    ],
                ],
                'labels' => ['Oliy', 'O\'rta maxsus', 'O\'rta'],
            ];
        }

        $employees = $this->record->employees;
        $higher = $employees->where('category', 'higher')->count();
        $secondarySpecial = $employees->where('category', 'secondary_special')->count();
        $secondary = $employees->where('category', 'secondary')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Xodimlar soni',
                    'data' => [$higher, $secondarySpecial, $secondary],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b'],
                ],
            ],
            'labels' => ['Oliy', 'O\'rta maxsus', 'O\'rta'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}