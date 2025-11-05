<?php

namespace App\Filament\Resources\Cadastres;

use App\Filament\Resources\Cadastres\Pages;
use App\Filament\Resources\Cadastres\Schemas\CadastreForm;
use App\Filament\Resources\Cadastres\Tables\CadastresTable;
use App\Models\Cadastre;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class CadastreResource extends Resource
{
    protected static ?string $model = Cadastre::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Kadastrlar';

    protected static ?string $modelLabel = 'Kadastr';

    protected static ?string $pluralModelLabel = 'Kadastrlar';

    protected static string|UnitEnum|null $navigationGroup = 'Boshqaruv';

    protected static ?int $navigationSort = 6;
    
    protected static ?string $recordTitleAttribute = 'name';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $form): Schema
    {
        return CadastreForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return CadastresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCadastres::route('/'),
            'create' => Pages\CreateCadastre::route('/create'),
            'edit' => Pages\EditCadastre::route('/{record}/edit'),
        ];
    }
}