<?php

namespace App\Filament\Resources\Stations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use App\Enums\StationType;

class StationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Stansiya nomi')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Turi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) =>
                        StationType::tryFrom($state)?->groupLabel() ?? '-'
                    )
                    ->color(fn (?string $state) =>
                        StationType::tryFrom($state)?->color() ?? 'gray'
                    )
                    ->sortable(),


                TextColumn::make('employees')
                    ->label('Xodimlar soni')
                    ->getStateUsing(fn ($record) => $record->employees()->count()),
                    
                ImageColumn::make('images')
                    ->label('Rasmlar')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('O\'zgartirilgan')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Turi')
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
            ->defaultSort('id', 'desc')
            ->striped()
            ->emptyStateHeading('Stansiyalar topilmadi')
            ->emptyStateDescription('Yangi stansiya qo\'shish uchun yuqoridagi "Yangi" tugmasini bosing.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}