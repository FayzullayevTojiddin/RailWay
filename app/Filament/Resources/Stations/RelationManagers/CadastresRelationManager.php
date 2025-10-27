<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\Cadastres\Schemas\CadastreForm;
use App\Filament\Resources\Cadastres\Tables\CadastresTable;

class CadastresRelationManager extends RelationManager
{
    protected static string $relationship = 'cadastres';

    protected static ?string $title = "Kadastrlar";

    protected static ?string $modelLabel = "Kadastr";
    
    protected static ?string $pluralModelLabel = "Kadastrlar";

    public function form(Schema $schema): Schema
    {
        return CadastreForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        $table = CadastresTable::configure($table);
        return $table
            ->headerActions([
                CreateAction::make()->label("Yangi qo'shish")
            ]);;
    }
}
