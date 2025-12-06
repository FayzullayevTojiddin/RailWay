<?php

namespace App\Filament\Resources\Rails;

use App\Filament\Resources\Rails\Pages\CreateRail;
use App\Filament\Resources\Rails\Pages\EditRail;
use App\Filament\Resources\Rails\Pages\ListRails;
use App\Filament\Resources\Rails\Schemas\RailForm;
use App\Filament\Resources\Rails\Tables\RailsTable;
use App\Models\Rail;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RailResource extends Resource
{
    protected static ?string $model = Rail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;
    
    protected static ?string $navigationLabel = 'Temir yo\'llar';
    
    protected static ?string $modelLabel = 'Temir yo\'l';
    
    protected static ?string $pluralModelLabel = 'Temir yo\'llar';

    protected static string | UnitEnum | null $navigationGroup = 'Boshqaruv';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $recordTitleAttribute = 'name';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();
        $total = static::getModel()::count();
        
        if ($total === 0) return 'gray';
        
        $percentage = ($count / $total) * 100;
        
        return $percentage > 80 ? 'success' : ($percentage > 50 ? 'warning' : 'danger');
    }

    public static function form(Schema $schema): Schema
    {
        return RailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RailsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // Agar Rail uchun qo'shimcha relationship'lar kerak bo'lsa shu yerga qo'shasiz
            // Masalan: schedules, maintenance, traffic va h.k.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRails::route('/'),
            'create' => CreateRail::route('/create'),
            'edit' => EditRail::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'type', 'status'];
    }
    
    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->name;
    }
    
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Stansiya' => $record->station?->title,
            'Turi' => $record->type,
            'Holati' => $record->status,
            'Uzunlik' => $record->length ? $record->length . ' km' : '-',
        ];
    }
}