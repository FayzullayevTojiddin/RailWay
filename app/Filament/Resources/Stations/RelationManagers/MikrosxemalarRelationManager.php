<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MikrosxemalarRelationManager extends RelationManager
{
    protected static string $relationship = 'mikrosxemalar';
    protected static ?string $title = 'Mikrosxemalar';
    protected static ?string $modelLabel = 'Mikrosxema';
    protected static ?string $pluralModelLabel = 'Mikrosxemalar';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mikrosxema ma\'lumotlari')
                    ->schema([
                        TextInput::make('nomi')
                            ->label('Nomi')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('ishlab_chiqarilgan_joyi')
                            ->label('Ishlab chiqarilgan joyi')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('biriktirilgan_shaxs')
                            ->label('Biriktirilgan shaxs')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(1),

                Section::make('Rasmlar')
                    ->schema([
                        FileUpload::make('rasmlar')
                            ->label('Rasmlar')
                            ->image()
                            ->multiple()
                            ->maxFiles(4)
                            ->directory('mikrosxemalar')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomi')
            ->columns([
                TextColumn::make('nomi')
                    ->label('Nomi')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('ishlab_chiqarilgan_joyi')
                    ->label('Ishlab chiqarilgan joyi')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('biriktirilgan_shaxs')
                    ->label('Biriktirilgan shaxs')
                    ->searchable()
                    ->alignCenter(),
                ImageColumn::make('rasmlar')
                    ->label('Rasm')
                    ->circular()
                    ->stacked()
                    ->alignCenter()
                    ->limit(2),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Yangi mikrosxema'),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Mikrosxemalar yo\'q')
            ->emptyStateDescription('Yangi mikrosxema qo\'shish uchun yuqoridagi tugmani bosing');
    }
}
