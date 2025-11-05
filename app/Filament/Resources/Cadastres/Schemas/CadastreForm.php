<?php

namespace App\Filament\Resources\Cadastres\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class CadastreForm
{
    public static function configure(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Asosiy ma\'lumotlar')
                ->schema([
                    Select::make('station_id')
                        ->label('Stansiya')
                        ->relationship('station', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('name')
                        ->label('Bino va inshootning nomi')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('cadastre_number')
                        ->label('Kadastr raqami')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('floors_count')
                        ->label('Qavat soni')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('qavat'),
                ])
                ->columns(1),

            Section::make('Maydon ma\'lumotlari')
                ->schema([
                    TextInput::make('construction_area')
                        ->label('Qurilish osti maydoni')
                        ->numeric()
                        ->suffix('kv.m')
                        ->step(0.01),

                    TextInput::make('total_area')
                        ->label('Umumiy maydoni')
                        ->numeric()
                        ->suffix('kv.m')
                        ->step(0.01),

                    TextInput::make('useful_area')
                        ->label('Umumiy foydali maydoni')
                        ->numeric()
                        ->suffix('kv.m')
                        ->step(0.01),
                ])
                ->columns(1),

            Section::make('Qo\'shimcha ma\'lumotlar')
                ->schema([
                    TextInput::make('details.passport_number')
                        ->label('Pasport raqami')
                        ->maxLength(255),

                    Textarea::make('details.location')
                        ->label('Manzil')
                        ->rows(2),

                    Textarea::make('details.land_category')
                        ->label('Yer kategoriyasi')
                        ->rows(2),

                    Textarea::make('details.land_type')
                        ->label('Yer turi')
                        ->rows(2),

                    Textarea::make('details.notes')
                        ->label('Qo\'shimcha izohlar')
                        ->rows(3)
                        ->columnSpan(2),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}