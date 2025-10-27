<?php

namespace App\Filament\Resources\Cadastres\Pages;

use App\Filament\Resources\Cadastres\CadastreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCadastres extends ListRecords
{
    protected static string $resource = CadastreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
