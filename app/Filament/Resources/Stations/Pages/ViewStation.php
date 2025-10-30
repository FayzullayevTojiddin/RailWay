<?php

namespace App\Filament\Resources\Stations\Pages;

use App\Filament\Resources\Stations\StationResource;
use Filament\Resources\Pages\ViewRecord;
use App\Livewire\EmployeesGenderChartWidget;
use App\Livewire\EmployeesCategoryChartWidget;
use App\Livewire\CadastreAreaChartWidget;
use Filament\Actions;

class ViewStation extends ViewRecord
{
    protected static string $resource = StationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Tahrirlash')
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->label('O\'chirish')
                ->icon('heroicon-o-trash'),
        ];
    }

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

    protected function getFooterWidgets(): array
    {
        return [
            //
        ];
    }

    public function getTitle(): string
    {
        return $this->record->title ?? 'Stansiya';
    }

    public function getBreadcrumb(): string
    {
        return 'Ko\'rish';
    }
}