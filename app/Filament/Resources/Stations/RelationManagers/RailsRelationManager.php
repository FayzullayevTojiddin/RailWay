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
use App\Filament\Resources\Rails\Schemas\RailForm;
use App\Filament\Resources\Rails\Tables\RailsTable;

class RailsRelationManager extends RelationManager
{
    protected static string $relationship = 'rails';

    protected static ?string $title = "Yo'llar";

    protected static ?string $modelLabel = "Yo'l";
    
    protected static ?string $pluralModelLabel = "Yo'llar";

    public function form(Schema $schema): Schema
    {
        return RailForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        $table = RailsTable::configure($table);
        return $table
            ->headerActions([
                CreateAction::make()->label("Yangi qo'shish")
            ]);
    }
}
