<?php

namespace App\Filament\Resources\Cadastres\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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
            Section::make()->schema([
                Section::make('Umumiy ma\'lumotlar')
                    ->schema([
                        Select::make('station_id')
                            ->label('Stansiya')
                            ->relationship('station', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        DatePicker::make('details.general.cadastre_date')
                            ->label('Kadastr yig\'majldi shakllantirigan sana')
                            ->displayFormat('d-M, Y')
                            ->native(false)
                            ->required(),

                        TextInput::make('details.general.passport_number')
                            ->label('Kadastr pasporti berilgan raqami')
                            ->required(),

                        TextInput::make('details.general.cadastre_number')
                            ->label('Yer uchastkasining kadastr raqami')
                            ->required(),

                        Textarea::make('details.general.location')
                            ->label('Joylashgan joyi')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Yer ma\'lumoti')
                    ->schema([
                        Textarea::make('details.land_info.object_name')
                            ->label('Yer uchastkasining (obyekt) nomi')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('details.land_info.land_category')
                            ->label('Yer uchastkasining toifasi')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('details.land_info.land_type')
                            ->label('Yer uchastkasining kichik toifasi')
                            ->required(),

                        Textarea::make('details.land_info.small_category')
                            ->label('Yerdan foydalanish turi')
                            ->rows(2)
                            ->required(),

                        TextInput::make('details.land_info.usage_type')
                            ->label('Yer uchastkasining kichik toifasi')
                            ->required(),

                        TextInput::make('details.land_info.transport_objects')
                            ->label('Temir yo\'l transporti obyektlari (bundan temiryol yo\'llari mustasno)')
                            ->default('-'),

                        TextInput::make('details.land_info.service_fee')
                            ->label('Servitut haqida ma\'lumot')
                            ->default('-'),
                    ])
                    ->columns(3),

                FileUpload::make('image')
                    ->label('Rasm')
                    ->image()
                    ->directory('cadastres')
                    ->imageEditor()
                    ->columnSpanFull(),
            ])->columnSpan(2),

            Section::make()->schema([
                Section::make('Bino va inshootlar tavsifi')
                    ->schema([
                        Repeater::make('items')
                            ->label('')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Bino va inshootning nomi')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('cadastre_code')
                                    ->label('Kadastr raqami')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('quantity')
                                    ->label('Qavat soni')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                TextInput::make('construction_area')
                                    ->label('Qurilish osti maydoni')
                                    ->suffix('kv.m')
                                    ->required(),

                                TextInput::make('total_area')
                                    ->label('Umumiy osti maydoni')
                                    ->suffix('kv.m')
                                    ->required(),

                                TextInput::make('useful_area')
                                    ->label('Umumiy foydali maydoni')
                                    ->suffix('kv.m')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Yangi bino')
                            ->addActionLabel('Bino/inshoot qo\'shish')
                            ->reorderable()
                            ->cloneable()
                            ->deleteAction(
                                fn ($action) => $action->requiresConfirmation()
                            ),
                    ]),
            ])->columnSpan(1),
        ])->columns(3);
    }
}