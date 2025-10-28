<?php

namespace App\Filament\Resources\Stations\Schemas;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Asosiy ma\'lumotlar')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('title')
                                ->label('Stansiya nomi')
                                ->required()
                                ->maxLength(255),

                            Select::make('type')
                                ->label('Ma\'lumot turi')
                                ->options([
                                    'station' => "Stansiya"
                                ])
                                ->searchable()
                                ->required(),
                        ]),

                    Textarea::make('description')
                        ->label('Ta\'rif')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Koordinatalar')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('coordinates.lat')
                                ->label('Kenglik (Latitude)')
                                ->numeric()
                                ->step(0.000001)
                                ->required()
                                ->placeholder('41.311151'),

                            TextInput::make('coordinates.lng')
                                ->label('Uzunlik (Longitude)')
                                ->numeric()
                                ->step(0.000001)
                                ->required()
                                ->placeholder('69.279737'),
                        ]),
                ]),

            Section::make('Qo\'shimcha ma\'lumotlar')
                ->description('Type tanlab, qo\'shimcha ma\'lumotlarni kiriting')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            // Yo'l kodi
                            TextInput::make('details.road_code')
                                ->label('Yo\'l kodi')
                                ->numeric()
                                ->visible(fn ($get) => $get('type') === 'road_code'),

                            // Temir yo'l tarmoqlari
                            TextInput::make('details.railway_branch_code')
                                ->label('Tarmoq kodi')
                                ->numeric()
                                ->visible(fn ($get) => $get('type') === 'railway_branches'),

                            // ESR stantsiya kodi
                            TextInput::make('details.esr_code')
                                ->label('ESR kodi')
                                ->numeric()
                                ->visible(fn ($get) => $get('type') === 'esr_code'),

                            // Mamlakat
                            Select::make('details.country')
                                ->label('Mamlakat')
                                ->options([
                                    'O\'zbekiston' => 'O\'zbekiston',
                                    'O\'zbekiston Temir Yo\'llari (OTY)' => 'O\'zbekiston Temir Yo\'llari (OTY)',
                                ])
                                ->visible(fn ($get) => $get('type') === 'country'),

                            // Yo'l
                            TextInput::make('details.road_name')
                                ->label('Yo\'l nomi')
                                ->visible(fn ($get) => $get('type') === 'road'),

                            // MTU nomi
                            TextInput::make('details.mtu_name')
                                ->label('MTU nomi')
                                ->visible(fn ($get) => $get('type') === 'mtu_name'),

                            // Stansiya toifasi
                            Select::make('details.station_category')
                                ->label('Toifa')
                                ->options([
                                    'Stansiya' => 'Stansiya',
                                    'Oraliq' => 'Oraliq',
                                ])
                                ->visible(fn ($get) => $get('type') === 'station_category'),

                            // Stansiya vazifasi
                            Select::make('details.station_function')
                                ->label('Vazifa')
                                ->options([
                                    'Oraliq' => 'Oraliq',
                                    'Yuk' => 'Yuk',
                                    'Texnik' => 'Texnik',
                                ])
                                ->visible(fn ($get) => $get('type') === 'station_function'),

                            // Stansiya Kategoriyasi
                            Select::make('details.station_class')
                                ->label('Kategoriya')
                                ->options([
                                    'Beshinchi' => 'Beshinchi',
                                    'To\'rtinchi' => 'To\'rtinchi',
                                    'Uchinchi' => 'Uchinchi',
                                    'Ikkinchi' => 'Ikkinchi',
                                    'Birinchi' => 'Birinchi',
                                ])
                                ->visible(fn ($get) => $get('type') === 'station_class'),

                            // Yuk ishlari
                            Select::make('details.cargo_operations')
                                ->label('Yuk ishlari')
                                ->options([
                                    'Yuk stantsiyasi' => 'Yuk stantsiyasi',
                                    'Ajiratilgan' => 'Ajiratilgan',
                                    'Ha' => 'Ha',
                                    'Yo\'q' => 'Yo\'q',
                                ])
                                ->visible(fn ($get) => $get('type') === 'cargo_operations'),

                            // Stansiya Tarifi qo'llanmasi
                            TextInput::make('details.tariff_code')
                                ->label('Tarif qo\'llanmasi')
                                ->visible(fn ($get) => $get('type') === 'tariff_application'),

                            // Stansiya yuk amallari
                            Select::make('details.cargo_actions')
                                ->label('Yuk amallari')
                                ->options([
                                    'Ha' => 'Ha',
                                    'Yo\'q' => 'Yo\'q',
                                ])
                                ->visible(fn ($get) => $get('type') === 'cargo_actions'),

                            // Punkt turi
                            Select::make('details.point_type')
                                ->label('Punkt turi')
                                ->options([
                                    'Ajiratilgan' => 'Ajiratilgan',
                                    'Ajratilgan' => 'Ajratilgan',
                                ])
                                ->visible(fn ($get) => $get('type') === 'point_type'),

                            // Tranzit punkt
                            Select::make('details.transit_point')
                                ->label('Tranzit punkt')
                                ->options([
                                    'Yo\'q' => 'Yo\'q',
                                    'Ha' => 'Ha',
                                ])
                                ->visible(fn ($get) => $get('type') === 'transit_point'),

                            // Faoliyat yurituvchi
                            Select::make('details.operator')
                                ->label('Operator')
                                ->options([
                                    'Ha' => 'Ha',
                                    'Yo\'q' => 'Yo\'q',
                                ])
                                ->visible(fn ($get) => $get('type') === 'operator'),

                            // Kenglik
                            TextInput::make('details.latitude_display')
                                ->label('Kenglik (ko\'rsatish)')
                                ->visible(fn ($get) => $get('type') === 'latitude'),

                            // Uzunlik
                            TextInput::make('details.longitude_display')
                                ->label('Uzunlik (ko\'rsatish)')
                                ->visible(fn ($get) => $get('type') === 'longitude'),

                            // Ob-Havo
                            TextInput::make('details.weather')
                                ->label('Ob-Havo')
                                ->suffix('°C')
                                ->visible(fn ($get) => $get('type') === 'weather'),
                        ]),
                ])
                ->collapsible()
                ->collapsed(false),

            Section::make('Bog\'lanish ma\'lumotlari')
                ->description('Stansiya uchun bog\'lanish ma\'lumotlarini kiriting')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('details.contact_name')
                                ->label('Ism familiya')
                                ->maxLength(255),

                            TextInput::make('details.contact_email')
                                ->label('Elektron pochta')
                                ->email()
                                ->maxLength(255),

                            TextInput::make('details.contact_phone')
                                ->label('Telefon raqam')
                                ->tel()
                                ->placeholder('+998 90 123 45 67')
                                ->maxLength(20),
                        ]),
                ])
                ->collapsible()
                ->collapsed(false),

            Section::make('Rasmlar')
                ->description('Stansiya rasmlarini yuklang (maksimal 10 ta)')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('images')
                        ->label('Stansiya rasmlari')
                        ->image()
                        ->multiple()
                        ->maxFiles(10)
                        ->directory('stations')
                        ->imageEditor()
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(false),
        ]);
    }
}