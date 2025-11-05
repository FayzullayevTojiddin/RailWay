<?php

namespace App\Filament\Resources\MainRailways\Pages;

use App\Filament\Resources\MainRailways\MainRailwayResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMainRailway extends EditRecord
{
    protected static string $resource = MainRailwayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
