<?php

namespace App\Filament\Resources\Stations\RelationManagers\Widgets;

use Filament\Widgets\Widget;

class ReportsWidget extends Widget
{
    protected string $view = 'filament.resources.stations.relation-managers.widgets.reports-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $selectedType = 'yuk_ortilishi';
    public ?int $stationId = null;

    public function mount(?int $stationId = null): void
    {
        $this->stationId = $stationId;
    }

    public function selectType(string $type): void
    {
        $this->selectedType = $type;
    }

    public function getChartData(): array
    {
        if (!$this->stationId) {
            return [
                'doughnut' => ['planned' => 0, 'actual' => 0],
                'bar' => [
                    'labels' => ['Yan', 'Fev', 'Mar', 'Apr', 'May', 'Iyun', 'Iyul', 'Avg', 'Sen', 'Okt', 'Noy', 'Dek'],
                    'planned' => array_fill(0, 12, 0),
                    'actual' => array_fill(0, 12, 0),
                ],
            ];
        }

        $reports = \App\Models\Report::where('station_id', $this->stationId)
            ->where('type', $this->selectedType)
            ->whereYear('date', now()->year)
            ->get();

        $totalPlanned = $reports->sum('planned_value');
        $totalActual = $reports->sum('actual_value');

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthReports = $reports->filter(fn($r) => $r->date->month === $i);
            $monthlyData[] = [
                'planned' => $monthReports->sum('planned_value'),
                'actual' => $monthReports->sum('actual_value'),
            ];
        }

        return [
            'doughnut' => [
                'planned' => $totalPlanned,
                'actual' => $totalActual,
            ],
            'bar' => [
                'labels' => ['Yan', 'Fev', 'Mar', 'Apr', 'May', 'Iyun', 'Iyul', 'Avg', 'Sen', 'Okt', 'Noy', 'Dek'],
                'planned' => array_column($monthlyData, 'planned'),
                'actual' => array_column($monthlyData, 'actual'),
            ],
        ];
    }
}