<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\Resources\Employees\Tables\EmployeesTable;
use App\Models\Employee;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    
    protected static ?string $navigationLabel = 'Xodimlar';
    
    protected static ?string $modelLabel = 'Xodim';
    
    protected static ?string $pluralModelLabel = 'Xodimlar';

    protected static string | UnitEnum | null $navigationGroup = 'Boshqaruv';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'first_name';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 50 ? 'success' : 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
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
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'phone', 'position'];
    }
    
    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->first_name . ' ' . $record->last_name;
    }
    
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Stansiya' => $record->station?->title,
            'Lavozim' => $record->position,
            'Email' => $record->email,
        ];
    }
}