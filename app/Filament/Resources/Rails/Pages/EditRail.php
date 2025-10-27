<?php

namespace App\Filament\Resources\Rails\Pages;

use App\Filament\Resources\Rails\RailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRail extends EditRecord
{
    protected static string $resource = RailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
