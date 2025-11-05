<?php

namespace App\Filament\Resources\BranchRailways\Tables;

use Filament\Actions\ActionGroup;
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

class BranchRailwaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Rasm')
                    ->square(),

                TextColumn::make('name')
                    ->label('Firma nomi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nusxalandi!'),

                TextColumn::make('stir')
                    ->label('STIR')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('station.title')
                    ->label('Stansiya')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('length')
                    ->label('Uzunlik')
                    ->suffix(' m')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('established_year')
                    ->label('Tashkil etilgan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

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
            ->emptyStateHeading('Shaxobcha yo\'llari topilmadi')
            ->emptyStateDescription('Yangi shaxobcha yo\'l qo\'shish uchun yuqoridagi tugmani bosing')
            ->defaultSort('created_at', 'desc');
    }
}
