<?php

namespace App\Filament\Resources\BranchRailways\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BranchRailwayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asosiy ma\'lumotlar')
                    ->schema([
                        Select::make('station_id')
                            ->label('Stansiya')
                            ->relationship('station', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label('Shaxobcha temir yo\'l egasi (Firma nomi)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('stir')
                            ->label('STIR raqami')
                            ->required()
                            ->numeric()
                            ->maxLength(9)
                            ->minLength(9),

                        FileUpload::make('image')
                            ->label('Rasm')
                            ->image()
                            ->directory('branch-railways')
                            ->imageEditor(),
                    ])
                    ->columns(1),

                Section::make('Egasi ma\'lumotlari')
                    ->schema([
                        TextInput::make('details.owner_full_name')
                            ->label('F.I.SH')
                            ->maxLength(255),

                        TextInput::make('details.owner_email')
                            ->label('Email')
                            ->email(),

                        TextInput::make('details.owner_phone')
                            ->label('Telefon raqam')
                            ->tel()
                            ->placeholder('+998 90 123 45 67'),
                    ])
                    ->columns(1),

                Section::make('Texnik ma\'lumotlar')
                    ->schema([
                        TextInput::make('length')
                            ->label('Uzunligi (m)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('m'),

                        TextInput::make('details.wagons_count')
                            ->label('Bir vaqtda berilgan vagonlarning soni')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('dona'),

                        TextInput::make('established_year')
                            ->label('Tashkil etilgan yil')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),

                        TextInput::make('details.technical_passport_year')
                            ->label('Texnik passport (yili)')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),

                        TextInput::make('details.directive_year')
                            ->label('Yo\'riqnoma (yili)')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),

                        TextInput::make('details.connection_point')
                            ->label('Tutashgan joyi')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Vagonlar masofasi')
                    ->schema([
                        Repeater::make('details.wagon_distances')
                            ->label('Vagonlarni qo\'yib olish masofasi')
                            ->schema([
                                TextInput::make('distance')
                                    ->label('Masofa')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Masofa qo\'shish')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                $state['distance'] ?? 'Yangi masofa'
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Shartnomalar')
                    ->schema([
                        Repeater::make('details.contracts')
                            ->label('Shartnoma')
                            ->schema([
                                TextInput::make('number')
                                    ->label('Raqam')
                                    ->required(),

                                DatePicker::make('start_date')
                                    ->label('Boshlanish sanasi')
                                    ->native(false)
                                    ->displayFormat('d.m.Y')
                                    ->required(),

                                DatePicker::make('end_date')
                                    ->label('Tugash sanasi')
                                    ->native(false)
                                    ->displayFormat('d.m.Y')
                                    ->required(),

                                FileUpload::make('file')
                                    ->label('Fayl')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->directory('contracts')
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Shartnoma qo\'shish')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                'Shartnoma ' . ($state['number'] ?? 'Noma\'lum')
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Yuk ishlari')
                    ->schema([
                        Repeater::make('details.cargo_work_periods')
                            ->label('Yuk ishlari muddati')
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Boshlanish')
                                    ->native(false)
                                    ->displayFormat('d.m.Y')
                                    ->required(),

                                DatePicker::make('end_date')
                                    ->label('Tugash')
                                    ->native(false)
                                    ->displayFormat('d.m.Y')
                                    ->required(),

                                TextInput::make('description')
                                    ->label('Izoh')
                                    ->maxLength(255),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Muddat qo\'shish')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                ($state['start_date'] ?? '') . ' - ' . ($state['end_date'] ?? '')
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Bekor qilish')
                    ->schema([
                        DatePicker::make('cancelled_at')
                            ->label('Bekor qilgan sana')
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
