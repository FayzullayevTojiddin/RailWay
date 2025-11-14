<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ReportChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Hisobotlar statistikasi';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Hisobotlar',
                    'data' => [10, 20, 30, 40, 50],
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => ['Yanv', 'Fev', 'Mart', 'Apr', 'May'],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // yoki 'line', 'pie', va h.k.
    }
}