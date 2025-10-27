<?php

namespace App\Filament\Resources\Rails\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asosiy ma\'lumotlar')
                    ->schema([
                        TextInput::make('serial_number')
                            ->label('T/R (Tartib raqami)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('station_id')
                            ->label('Stansiya')
                            ->relationship('station', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('details.rail_owner')
                            ->label('Shahobcha temir yo\'l egasi')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('details.stir')
                            ->label('STIR')
                            ->numeric()
                            ->maxLength(9)
                            ->minLength(9),

                        FileUpload::make('image')
                            ->label('Rasmi')
                            ->image()
                            ->directory('rails')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Egasi ma\'lumotlari')
                    ->schema([
                        TextInput::make('details.owner_full_name')
                            ->label('F.I.O')
                            ->maxLength(255),

                        TextInput::make('details.owner_position')
                            ->label('Lavozimi')
                            ->maxLength(255),

                        TextInput::make('details.owner_email')
                            ->label('Email')
                            ->email(),

                        TextInput::make('details.owner_phone')
                            ->label('Telefon')
                            ->tel()
                            ->placeholder('+998 90 123 45 67'),
                    ])
                    ->columns(2),

                Section::make('Texnik ma\'lumotlar')
                    ->schema([
                        TextInput::make('length')
                            ->label('Uzunligi (m)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('m'),

                        TextInput::make('wagon_counts')
                            ->label('Bir vaqtda beriladigan vagonlar soni')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        TextInput::make('details.established_year')
                            ->label('Tashkil etilgan yili (tex.pasport bo\'yicha)')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),

                        TextInput::make('direction')
                            ->label('Yo\'riqnoma')
                            ->maxLength(255),

                        TextInput::make('front')
                            ->label('Front')
                            ->maxLength(255),

                        TextInput::make('loading_joy')
                            ->label('Tutashgan joyi')
                            ->maxLength(255),

                        TextInput::make('technical_passport')
                            ->label('Texnik pasport')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Vagonlar ma\'lumoti')
                    ->schema([
                        Repeater::make('details.wagon_distances')
                            ->label('Vagonlarni qo\'yib olish masofasi')
                            ->schema([
                                TextInput::make('number')
                                    ->label('Raqam')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('distance')
                                    ->label('Masofa')
                                    ->maxLength(255)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Masofa qo\'shish')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                ($state['number'] ?? '') . ' - ' . ($state['distance'] ?? '')
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

                                DatePicker::make('payment_date')
                                    ->label('To\'lov sanasi')
                                    ->native(false)
                                    ->displayFormat('d.m.Y'),

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
                            ->label('Bekor qilingan sana')
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}