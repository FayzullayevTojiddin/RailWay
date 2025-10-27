<?php

namespace App\Filament\Resources\Cadastres\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;

class CadastresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Rasm')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('station.name')
                    ->label('Stansiya')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('details.general.cadastre_number')
                    ->label('Kadastr raqami')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('details.general.passport_number')
                    ->label('Pasport raqami')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('details.general.cadastre_date')
                    ->label('Sana')
                    ->date('d-M, Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('details.general.location')
                    ->label('Joylashuv')
                    ->limit(40)
                    ->searchable()
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('details.land_info.object_name')
                    ->label('Obyekt nomi')
                    ->limit(40)
                    ->searchable()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('items_count')
                    ->label('Binolar soni')
                    ->getStateUsing(fn ($record) => is_array($record->items) ? count($record->items) : 0)
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d-M, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('O\'zgartirilgan')
                    ->dateTime('d-M, Y H:i')
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
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}