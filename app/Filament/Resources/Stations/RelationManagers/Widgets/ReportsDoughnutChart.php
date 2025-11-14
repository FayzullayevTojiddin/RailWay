<?php

namespace App\Filament\Resources\Stations\RelationManagers\Widgets;

use Filament\Widgets\ChartWidget;

class ReportsDoughnutChart extends ChartWidget
{
    public ?string $filterType = null;

    protected ?string $heading = null;

    protected static ?int $contentHeight = 320;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $mock = [
            'yuk_ortilishi' => [1065, 1010],
            'yuk_tushishi'  => [660, 620],
            'vagon_kirish'  => [363, 338],
            'vagon_chiqish' => [253, 229],
            'boshqalar'     => [69, 62],
        ];

        $type = $this->filterType ?? 'yuk_ortilishi';
        $vals = $mock[$type] ?? $mock['yuk_ortilishi'];

        return [
            'labels' => ['Reja', 'Haqiqiy'],
            'datasets' => [
                [
                    'data' => [$vals[0], $vals[1]],
                ],
            ],
        ];
    }
}
