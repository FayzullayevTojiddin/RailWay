<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shaxsiy ma\'lumotlar')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Rasm')
                            ->image()
                            ->directory('employees')
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpanFull(),

                        TextInput::make('full_name')
                            ->label('To\'liq ismi')
                            ->required()
                            ->maxLength(255),

                        Select::make('sex')
                            ->label('Jinsi')
                            ->options([
                                'male' => 'Erkak',
                                'female' => 'Ayol',
                            ])
                            ->required()
                            ->native(false),

                        DatePicker::make('birth_date')
                            ->label('Tug\'ilgan sanasi')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y'),

                        TextInput::make('phone_number')
                            ->label('Telefon raqami')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('+998 90 123 45 67'),

                        Select::make('document_type')
                            ->label('Ish turi')
                            ->options([
                                'main' => 'Asosiy ish',
                                'additional' => 'Qo\'shimcha ish',
                            ])
                            ->required()
                            ->native(false)
                            ->default('main'),
                        
                        Select::make('category')
                            ->label("Ma'lumoti")
                            ->options([
                                'higher' => 'Oliy',
                                'secondary_special' => 'O‘rta maxsus',
                                'secondary' => 'O‘rta',
                            ])
                    ])
                    ->columns(2),

                Section::make('Ish ma\'lumotlari')
                    ->schema([
                        Select::make('station_id')
                            ->label('Stansiya')
                            ->relationship('station', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('role')
                            ->label('Lavozimi')
                            ->placeholder('Masalan: Administrator')
                            ->required(),

                        DateTimePicker::make('joined_at')
                            ->label('Ishga kirgan sanasi')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                    ])
                    ->columns(3),

                Section::make('Hujjatlar')
                    ->schema([
                        FileUpload::make('details.pdf_document')
                            ->label('PDF hujjat')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('employee-documents')
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->previewable(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Qo\'shimcha ma\'lumotlar')
                    ->schema([
                        TextInput::make('details.birth_place')
                            ->label('Tug\'ilgan joyi')
                            ->maxLength(255),

                        TextInput::make('details.nationality')
                            ->label('Millati')
                            ->maxLength(255)
                            ->default('O\'zbek'),

                        TextInput::make('details.party_membership')
                            ->label('Partiyaviyligi')
                            ->maxLength(255)
                            ->default('Partiyasiz'),

                        TextInput::make('details.education_level')
                            ->label('Ma\'lumoti')
                            ->maxLength(255),

                        TextInput::make('details.education_institution')
                            ->label('Tamomlagan')
                            ->maxLength(255),

                        TextInput::make('details.education_year')
                            ->label('Tamomlagan yili')
                            ->numeric()
                            ->minValue(1950)
                            ->maxValue(date('Y')),

                        TextInput::make('details.specialty')
                            ->label('Mutaxassisligi')
                            ->maxLength(255),

                        TextInput::make('details.academic_degree')
                            ->label('Ilmiy darajasi')
                            ->maxLength(255)
                            ->default('Yo\'q'),

                        TextInput::make('details.academic_title')
                            ->label('Ilmiy unvoni')
                            ->maxLength(255)
                            ->default('Yo\'q'),

                        TextInput::make('details.foreign_languages')
                            ->label('Chet tillar')
                            ->maxLength(255),

                        TextInput::make('details.military_rank')
                            ->label('Harbiy unvoni')
                            ->maxLength(255)
                            ->default('Yo\'q'),

                        Textarea::make('details.awards')
                            ->label('Davlat mukofotlari')
                            ->rows(2)
                            ->maxLength(500),

                        Textarea::make('details.elected_positions')
                            ->label('Saylanadigan organlar a\'zoligi')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Mehnat faoliyati')
                    ->schema([
                        Repeater::make('details.work_history')
                            ->label('Ish tajribasi')
                            ->schema([
                                TextInput::make('period')
                                    ->label('Davr')
                                    ->placeholder('2020-2024')
                                    ->required(),

                                Textarea::make('position')
                                    ->label('Lavozim va ish joyi')
                                    ->rows(2)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Ish tajribasi qo\'shish')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['period'] ?? null),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Oilaviy ma\'lumotlar')
                    ->schema([
                        Repeater::make('details.family_members')
                            ->label('Qarindoshlar')
                            ->schema([
                                Select::make('relation')
                                    ->label('Qarindoshligi')
                                    ->options([
                                        'father' => 'Otasi',
                                        'mother' => 'Onasi',
                                        'spouse' => 'Turmush o\'rtog\'i',
                                        'son' => 'O\'g\'li',
                                        'daughter' => 'Qizi',
                                        'brother' => 'Akasi/Ukasi',
                                        'sister' => 'Singlisi/Opasi',
                                        'father_in_law' => 'Qaynotasi',
                                        'mother_in_law' => 'Qaynonasi',
                                    ])
                                    ->required()
                                    ->native(false),

                                TextInput::make('full_name')
                                    ->label('F.I.O')
                                    ->required(),

                                TextInput::make('birth_year')
                                    ->label('Tug\'ilgan yili')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y')),

                                TextInput::make('birth_place')
                                    ->label('Tug\'ilgan joyi')
                                    ->maxLength(255),

                                Textarea::make('work_place')
                                    ->label('Ish joyi va lavozimi')
                                    ->rows(2),

                                TextInput::make('address')
                                    ->label('Turar joyi')
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Qarindosh qo\'shish')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                ($state['relation'] ?? '') . ' - ' . ($state['full_name'] ?? 'Noma\'lum')
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}