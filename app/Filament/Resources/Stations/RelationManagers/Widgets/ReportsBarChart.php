<?php

namespace App\Filament\Resources\Stations\RelationManagers\Widgets;

use Filament\Widgets\ChartWidget;

class ReportsBarChart extends ChartWidget
{
    public ?string $filterType = null;

    protected ?string $heading = null; // headingni blade joyida ko'rsatamiz

    // Bu height wrapper ichidagi balandlikka mos bo'lsin
    protected static ?int $contentHeight = 320;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        // mock data (filterType bo'yicha)
        $mock = [
            'yuk_ortilishi' => [120,150,130,170,160,155,180],
            'yuk_tushishi'  => [80,90,95,100,92,88,105],
            'vagon_kirish'  => [40,50,45,60,55,48,65],
            'vagon_chiqish' => [30,35,33,32,40,38,45],
            'boshqalar'     => [10,12,15,8,14,9,11],
        ];

        $labels = ['01','02','03','04','05','06','07'];
        $type = $this->filterType ?? 'yuk_ortilishi';
        $data = $mock[$type] ?? $mock['yuk_ortilishi'];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Reja',
                    'data' => $data,
                    // Filament/Chart.js default ranglarni ishlatsa ham bo'ladi;
                    // agar xohlasangiz color ni qo'shamiz
                ],
            ],
        ];
    }
}
