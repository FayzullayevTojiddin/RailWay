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
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'big_station'   => 'Katta stansiya',
                        'small_station' => 'Kichik stansiya',
                        'bridge'        => 'Ko\'prik',
                        'enterprise'    => 'Korxona',
                        default         => $state ?? '',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'big_station'   => 'primary',
                        'small_station' => 'info',
                        'bridge'        => 'warning',
                        'enterprise'    => 'success',
                        default         => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Ta\'rif')
                    ->limit(50),
                    
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
                    ->options([
                        'road_code' => 'Yo\'l kodi',
                        'railway_branches' => 'Temir yo\'l tarmoqlari',
                        'esr_code' => 'ESR kodi',
                        'country' => 'Mamlakat',
                        'road' => 'Yo\'l',
                        'mtu_name' => 'MTU nomi',
                        'station_category' => 'Toifa',
                        'station_function' => 'Vazifa',
                        'station_class' => 'Kategoriya',
                        'cargo_operations' => 'Yuk ishlari',
                        'tariff_application' => 'Tarif',
                        'cargo_actions' => 'Yuk amallari',
                        'point_type' => 'Punkt turi',
                        'transit_point' => 'Tranzit',
                        'operator' => 'Operator',
                        'latitude' => 'Kenglik',
                        'longitude' => 'Uzunlik',
                        'weather' => 'Ob-Havo',
                    ])
                    ->multiple(),
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