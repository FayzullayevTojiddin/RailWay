<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    
    protected static ?string $navigationLabel = 'Foydalanuvchilar';
    
    protected static ?string $modelLabel = 'Foydalanuvchi';
    
    protected static ?string $pluralModelLabel = 'Foydalanuvchilar';
    
    protected static string|UnitEnum|null $navigationGroup = 'Tizim';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'name';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $activeUsers = static::getModel()::where('is_active', true)->count();
        $totalUsers = static::getModel()::count();
        
        if ($totalUsers === 0) return 'gray';
        
        $percentage = ($activeUsers / $totalUsers) * 100;
        
        return $percentage > 80 ? 'success' : ($percentage > 50 ? 'warning' : 'danger');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // Agar User uchun qo'shimcha relationship'lar kerak bo'lsa
            // Masalan: activities, permissions, roles va h.k.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone'];
    }
    
    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->name;
    }
    
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Email' => $record->email,
            'Telefon' => $record->phone ?? '-',
            'Holati' => $record->is_active ? 'Aktiv' : 'Nofaol',
        ];
    }
}