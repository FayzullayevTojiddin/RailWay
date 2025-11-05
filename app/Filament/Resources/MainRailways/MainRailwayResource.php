<?php

namespace App\Filament\Resources\MainRailways;

use App\Filament\Resources\MainRailways\Pages\CreateMainRailway;
use App\Filament\Resources\MainRailways\Pages\EditMainRailway;
use App\Filament\Resources\MainRailways\Pages\ListMainRailways;
use App\Filament\Resources\MainRailways\Schemas\MainRailwayForm;
use App\Filament\Resources\MainRailways\Tables\MainRailwaysTable;
use App\Models\MainRailway;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MainRailwayResource extends Resource
{
    protected static ?string $model = MainRailway::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;
    
    protected static ?string $navigationLabel = 'Temir yo\'llar';
    
    protected static ?string $modelLabel = 'Temir yo\'l';
    
    protected static ?string $pluralModelLabel = 'Temir yo\'llar';

    protected static string | UnitEnum | null $navigationGroup = 'Boshqaruv';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $recordTitleAttribute = 'name';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return MainRailwayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MainRailwaysTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMainRailways::route('/'),
            'create' => CreateMainRailway::route('/create'),
            'edit' => EditMainRailway::route('/{record}/edit'),
        ];
    }
}
