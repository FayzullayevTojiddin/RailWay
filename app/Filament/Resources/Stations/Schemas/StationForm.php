<?php

namespace App\Filament\Resources\Stations\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use App\Enums\StationType;

class StationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make("Asosiy ma'lumotlar")
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('title')
                                ->label('Stansiya nomi')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),

                           Select::make('type')
                                ->label("Ma'lumot turi")
                                ->options(StationType::options())
                                ->required(),
                        ]),

                    Textarea::make('description')
                        ->label('Ta\'rif')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Koordinatalar')
                ->description('GPS koordinatlari va xarita pozitsiyasi')
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

                    Grid::make(2)
                        ->schema([
                            TextInput::make('coordinates.x')
                                ->label('X - Gorizontal pozitsiya (%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%')
                                ->helperText("Xaritada chapdan o'ngga (0-100)")
                                ->placeholder('50')
                                ->rules([
                                    'nullable',
                                    'regex:/^\d+(\.\d{1,2})?$/'
                                ])
                                ->dehydrateStateUsing(fn($state) => $state === null ? null : round((float)$state, 2)),

                            TextInput::make('coordinates.y')
                                ->label('Y - Vertikal pozitsiya (%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%')
                                ->helperText('Xaritada tepadan pastga (0-100)')
                                ->placeholder('50')
                                ->rules([
                                    'nullable',
                                    'regex:/^\d+(\.\d{1,2})?$/'
                                ])
                                ->dehydrateStateUsing(fn($state) => $state === null ? null : round((float)$state, 2)),
                        ]),
                ])
                ->footerActions([
                    Action::make('viewOnMap')
                        ->label('Sputnikdan ko\'rish')
                        ->icon('heroicon-o-globe-alt')
                        ->url(fn ($get) => 'https://www.google.com/maps?q=' . ($get('coordinates.lat') ?? '41.311151') . ',' . ($get('coordinates.lng') ?? '69.279737') . '&t=k')
                        ->openUrlInNewTab()
                        ->color('success')
                        ->size('lg')
                        ->extraAttributes(['class' => 'w-full justify-center'])
                        ->visible(fn ($get) => $get('coordinates.lat') && $get('coordinates.lng')),
                ])
                ->footerActionsAlignment(Alignment::Center)
                ->collapsible()
                ->collapsed(false),

            Section::make("Stansiya ma'lumotlari")
                ->schema([
                    TextInput::make('details.station_class')
                        ->label("Stantsiyaning classi")
                        ->numeric()
                        ->step(1)
                        ->rules(['nullable', 'integer']),

                    TextInput::make('details.receiving_tracks')
                        ->label("Qabul qilib jo'natuvchi yo'llar")
                        ->numeric()
                        ->step(1)
                        ->rules(['nullable', 'integer']),

                    TextInput::make('details.traction_tracks')
                        ->label("Traksion yo'llar")
                        ->numeric()
                        ->step(1)
                        ->rules(['nullable', 'integer']),
                ])
                ->visible(fn ($get) => in_array(
                    $get('type'),
                    [
                        StationType::SMALL_STATION->value,
                        StationType::BIG_STATION->value,
                    ]
                ))
                ->columns(3)
                ->columnSpanFull(),

            Section::make("Qo'shimcha ma'lumotlari")
                ->columnSpanFull()
                ->schema([
                    RichEditor::make('details.additional')
                        ->label("Qo'shimcha ma'lumotlari")
                        ->columnSpanFull(),
                ])
                ->visible(fn ($get) => in_array(
                    $get('type'),
                    [
                        StationType::ENTERPRISE_PCH->value,
                        StationType::ENTERPRISE_TCH->value,
                        StationType::ENTERPRISE_SHCH->value,
                        StationType::ENTERPRISE_ECH->value,
                        StationType::ENTERPRISE_TO->value,
                        StationType::ENTERPRISE_PMS->value,
                        StationType::ENTERPRISE_VCHD->value,
                        StationType::ENTERPRISE_RJU->value,
                    ]
                ))
                ->nullable()
                ->collapsible(),

            Section::make('360° ko\'rish')
                ->description('Stansiyani 360 daraja ko\'rish uchun havola')
                ->schema([
                    TextInput::make('details.360_link')
                        ->label('360° panorama havolasi')
                        ->url()
                        ->placeholder('https://example.com/360-view')
                        ->helperText('360 daraja panorama ko\'rinish uchun URL kiriting')
                        ->suffixIcon('heroicon-o-globe-alt')
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(false),

            Section::make('Kadastr rasmi')
                ->description('Stansiya kadastr pasporti yoki xaritasining rasmi (1 ta rasm)')
                ->schema([
                    FileUpload::make('details.cadastre_image')
                        ->label('Kadastr rasmi')
                        ->image()
                        ->directory('cadastres')
                        ->imageEditor()
                        ->imagePreviewHeight('250')
                        ->maxSize(20480)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                        ->helperText('Faqat 1 ta rasm. Maksimal hajm: 20MB')
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(false),

            Section::make('Bog\'lanish ma\'lumotlari')
                ->description('Stansiya uchun bog\'lanish ma\'lumotlarini kiriting')
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
                ])
                ->collapsible()
                ->collapsed(false),

            Section::make('Rasmlar')
                ->description('Stansiya rasmlarini yuklang (maksimal 10 ta)')
                ->schema([
                    FileUpload::make('images')
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