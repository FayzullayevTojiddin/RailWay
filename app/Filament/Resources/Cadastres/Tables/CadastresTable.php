<?php

namespace App\Filament\Resources\Cadastres\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;

class CadastresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('station.title')
                    ->label('Stansiya')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('name')
                    ->label('Bino/inshoot nomi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('cadastre_number')
                    ->label('Kadastr raqami')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nusxalandi!')
                    ->color('primary'),

                TextColumn::make('floors_count')
                    ->label('Qavat soni')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),

                TextColumn::make('construction_area')
                    ->label('Qurilish osti')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kv.m')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('total_area')
                    ->label('Umumiy maydon')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kv.m')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('useful_area')
                    ->label('Foydali maydon')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kv.m')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('details.passport_number')
                    ->label('Pasport №')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label("Ko'rish"),
                    EditAction::make()->label("Tahrirlash"),
                    DeleteAction::make()->label("O'chirish"),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label("O'chirish"),
                ]),
            ])
            ->emptyStateHeading('Kadastrlar topilmadi')
            ->emptyStateDescription('Yangi kadastr qo\'shish uchun yuqoridagi tugmani bosing')
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}