<?php

namespace App\Filament\Resources\MainRailways\Pages;

use App\Filament\Resources\MainRailways\MainRailwayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMainRailways extends ListRecords
{
    protected static string $resource = MainRailwayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
