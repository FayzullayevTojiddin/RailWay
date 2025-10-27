<?php

namespace App\Filament\Resources\Cadastres\Pages;

use App\Filament\Resources\Cadastres\CadastreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCadastre extends EditRecord
{
    protected static string $resource = CadastreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
