<?php

namespace App\Filament\Resources\BranchRailways\Pages;

use App\Filament\Resources\BranchRailways\BranchRailwayResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBranchRailway extends EditRecord
{
    protected static string $resource = BranchRailwayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
