<?php

namespace App\Filament\Resources\Stations\Pages;

use App\Filament\Resources\Stations\StationResource;
use Filament\Resources\Pages\EditRecord;
use App\Livewire\EmployeesGenderChartWidget;
use App\Livewire\EmployeesCategoryChartWidget;
use App\Livewire\CadastreAreaChartWidget;
use App\Filament\Resources\Stations\RelationManagers\Widgets\ReportsWidget;

class EditStation extends EditRecord
{
    protected static string $resource = StationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            EmployeesGenderChartWidget::class,
            EmployeesCategoryChartWidget::class,
            CadastreAreaChartWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }

    public function getFooterWidgets(): array
    {
        return [
            // ReportsWidget::class,
        ];
    }
}