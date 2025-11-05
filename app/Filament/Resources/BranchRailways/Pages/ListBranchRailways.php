<?php

namespace App\Filament\Resources\BranchRailways\Pages;

use App\Filament\Resources\BranchRailways\BranchRailwayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBranchRailways extends ListRecords
{
    protected static string $resource = BranchRailwayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
