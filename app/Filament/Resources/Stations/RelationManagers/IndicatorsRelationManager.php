<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndicatorsRelationManager extends RelationManager
{
    protected static string $relationship = 'indicators';

    protected static ?string $title = "Iqtisodiy ko'rsatkichlar";
    protected static ?string $modelLabel = "Iqtisodiy ko'rsatkich";
    protected static ?string $pluralModelLabel = "Iqtisodiy ko'rsatkichlar";

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('file')
                ->label("Hujjat")
                ->directory('economic-indicators')
                ->downloadable()
                ->imagePreviewHeight('600')
                ->panelAspectRatio('16:9')
                ->panelLayout('integrated')

                ->openable()

                ->acceptedFileTypes([
                        'image/*',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    
                ->columnSpanFull(),

            TextInput::make('title')
                ->label("Ko'rsatkich nomi")
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('description')
                ->label("Tavsif")
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label("Ko'rsatkich nomi")
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label("Tavsif")
                    ->limit(50),

                TextColumn::make('created_at')
                    ->label("Yaratilgan sana")
                    ->dateTime('d.m.Y'),
            ])
            ->headerActions([
                CreateAction::make()->label("Yangi iqtisodiy ko'rsatkich"),
            ])
            ->actions([
                EditAction::make()
                    ->label("Tahrirlash")
                    ->button(),
                DeleteAction::make()
                    ->label("O'chirish")
                    ->button(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}