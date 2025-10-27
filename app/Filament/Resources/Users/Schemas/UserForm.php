<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Foydalanuvchi ma\'lumotlari')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Ism familiya')
                                ->required()
                                ->maxLength(255),

                            Select::make('role')
                                ->label('Rol')
                                ->options([
                                    'super' => 'SuperAdmin',
                                    'manager' => 'Boshqaruvchi',
                                    'viewer' => 'Ko\'ruvchi',
                                ])
                                ->required()
                                ->default('viewer')
                                ->native(false),
                        ]),
                ]),

            Section::make('Kirish ma\'lumotlari')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),

                            TextInput::make('password')
                                ->label('Parol')
                                ->password()
                                ->revealable()
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $context): bool => $context === 'create')
                                ->minLength(8)
                                ->maxLength(255)
                                ->helperText('Kamida 8 ta belgi')
                                ->autocomplete('new-password'),
                        ]),
                ]),
        ]);
    }
}