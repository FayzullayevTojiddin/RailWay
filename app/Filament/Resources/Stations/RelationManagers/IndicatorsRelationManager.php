<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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

    public function isReadOnly(): bool
    {
        $referer = request()->header('referer') ?? '';
        return ! str_contains($referer, '/edit');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('file')
                ->label("Hujjat")
                ->directory('economic-indicators')
                ->downloadable()
                ->imagePreviewHeight('80vh')
                ->panelAspectRatio('21:9')
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
        $referer = request()->header('referer') ?? '';
        $isEditMode = str_contains($referer, '/edit');

        return $table
            ->recordAction('view')
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label("Ko'rsatkich nomi")
                    ->searchable()
                    ->limit(30)
                    ->sortable(),

                TextColumn::make('description')
                    ->label("Tavsif")
                    ->limit(50),

                TextColumn::make('created_at')
                    ->label("Yaratilgan sana")
                    ->dateTime('d.m.Y'),
            ])
            ->headerActions(
                $isEditMode ? [
                    CreateAction::make()->label("Yangi iqtisodiy ko'rsatkich"),
                ] : []
            )
            ->actions(
                $isEditMode ? [
                    EditAction::make()->label("Tahrirlash")->button(),
                    DeleteAction::make()->label("O'chirish")->button(),
                ] : [
                    ViewAction::make()->label("Ko'rish")->button(),
                ]
            )
            ->bulkActions(
                $isEditMode ? [
                    DeleteBulkAction::make(),
                ] : []
            );
    }
}