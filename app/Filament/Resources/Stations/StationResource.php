<?php

namespace App\Filament\Resources\Stations;

use App\Filament\Resources\Stations\Pages\CreateStation;
use App\Filament\Resources\Stations\Pages\EditStation;
use App\Filament\Resources\Stations\Pages\ViewStation;
use App\Filament\Resources\Stations\Pages\ListStations;
use App\Filament\Resources\Stations\Schemas\StationForm;
use App\Filament\Resources\Stations\Tables\StationsTable;
use App\Filament\Resources\Stations\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\Stations\RelationManagers\BranchRailwaysRelationManager;
use App\Filament\Resources\Stations\RelationManagers\MainRailwaysRelationManager;
use App\Models\Station;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\Stations\RelationManagers\CadastresRelationManager;
use App\Filament\Resources\Stations\RelationManagers\ReportsRelationManager;

class StationResource extends Resource
{
    protected static ?string $model = Station::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    
    protected static ?string $navigationLabel = 'Stansiyalar';
    
    protected static ?string $modelLabel = 'Stansiya';
    
    protected static ?string $pluralModelLabel = 'Stansiyalar';
    
    protected static string|UnitEnum|null $navigationGroup = 'Boshqaruv';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'title';

    public function getHeaderWidgets(): array
    {
        return [
            //
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return StationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EmployeesRelationManager::class,
            BranchRailwaysRelationManager::class,
            MainRailwaysRelationManager::class,
            CadastresRelationManager::class,
            ReportsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStations::route('/'),
            'create' => CreateStation::route('/create'),
            'edit' => EditStation::route('/{record}/edit'),
            'show' => ViewStation::route('/{record}'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'type', 'description'];
    }
    
    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->title;
    }
    
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Turi' => $record->type,
            'Xodimlar' => $record->employees()->count() . ' ta',
            'Shaxobcha yo\'llar' => $record->branchRailways()->count() . ' ta',
            'Temir yo\'llar' => $record->mainRailways()->count() . ' ta',
        ];
    }
}