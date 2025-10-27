<?php

namespace App\Filament\Resources\Rails\Pages;

use App\Filament\Resources\Rails\RailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRails extends ListRecords
{
    protected static string $resource = RailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
