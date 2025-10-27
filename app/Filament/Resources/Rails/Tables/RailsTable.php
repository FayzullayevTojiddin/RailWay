<?php

namespace App\Filament\Resources\Rails\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;

class RailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Rasm')
                    ->square(),

                TextColumn::make('serial_number')
                    ->label('Seriya raqami')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nusxalandi!'),

                TextColumn::make('station.title')
                    ->label('Stansiya')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('length')
                    ->label('Uzunlik')
                    ->suffix(' m')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('wagon_counts')
                    ->label('Vagonlar')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                TextColumn::make('direction')
                    ->label('Yo\'nalish')
                    ->searchable()
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('front')
                    ->label('Front')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('loading_joy')
                    ->label('Yuklash joyi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('cancelled_at')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->getStateUsing(fn ($record) => $record->cancelled_at !== null)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('station_id')
                    ->label('Stansiya')
                    ->relationship('station', 'title')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('cancelled')
                    ->label('Bekor qilingan')
                    ->query(fn ($query) => $query->whereNotNull('cancelled_at'))
                    ->toggle(),

                Filter::make('active')
                    ->label('Faol')
                    ->query(fn ($query) => $query->whereNull('cancelled_at'))
                    ->toggle()
                    ->default(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ko\'rish'),
                    EditAction::make()
                        ->label('Tahrirlash'),
                    DeleteAction::make()
                        ->label('O\'chirish'),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('O\'chirish'),
                ]),
            ])
            ->emptyStateHeading('Yo\'llar topilmadi')
            ->emptyStateDescription('Yangi yo\'l qo\'shish uchun yuqoridagi tugmani bosing')
            ->defaultSort('created_at', 'desc');
    }
}