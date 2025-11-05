<?php

namespace App\Filament\Resources\BranchRailways;

use App\Filament\Resources\BranchRailways\Pages\CreateBranchRailway;
use App\Filament\Resources\BranchRailways\Pages\EditBranchRailway;
use App\Filament\Resources\BranchRailways\Pages\ListBranchRailways;
use App\Filament\Resources\BranchRailways\Schemas\BranchRailwayForm;
use App\Filament\Resources\BranchRailways\Tables\BranchRailwaysTable;
use App\Models\BranchRailway;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BranchRailwayResource extends Resource
{
    protected static ?string $model = BranchRailway::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    
    protected static ?string $navigationLabel = 'Shaxobcha yo\'llari';
    
    protected static ?string $modelLabel = 'Shaxobcha yo\'l';
    
    protected static ?string $pluralModelLabel = 'Shaxobcha yo\'llari';

    protected static string | UnitEnum | null $navigationGroup = 'Boshqaruv';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $recordTitleAttribute = 'name';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return BranchRailwayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchRailwaysTable::configure($table);
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
            'index' => ListBranchRailways::route('/'),
            'create' => CreateBranchRailway::route('/create'),
            'edit' => EditBranchRailway::route('/{record}/edit'),
        ];
    }
}
