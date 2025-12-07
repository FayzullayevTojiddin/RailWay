<?php

namespace App\Filament\Resources\Employees\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\ActionGroup;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Rasm')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('full_name')
                    ->label('To\'liq ismi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('phone_number')
                    ->label('Telefon')
                    ->searchable()
                    ->icon('heroicon-m-phone'),

                TextColumn::make('station.title')
                    ->label('Stansiya')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('role')
                    ->label('Lavozim')
                    ->limit(20)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category')
                    ->label("Ma'lumoti")
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'higher' => 'Oliy',
                        'secondary_special' => 'O‘rta maxsus',
                        'secondary' => 'O‘rta',
                        default => '-',
                    }),

                TextColumn::make('sex')
                    ->label('Jinsi')
                    ->formatStateUsing(fn (string $state): string => $state === 'male' ? 'Erkak' : 'Ayol')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'male' ? 'blue' : 'pink')
                    ->toggleable(),

                TextColumn::make('birth_date')
                    ->label('Tug\'ilgan sana')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('joined_at')
                    ->label('Ishga kirgan')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(),

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

                SelectFilter::make('role')
                    ->label('Lavozim')
                    ->options([
                        'admin' => 'Administrator',
                        'manager' => 'Menejer',
                        'operator' => 'Operator',
                        'dispatcher' => 'Dispatcher',
                    ])
                    ->multiple(),

                SelectFilter::make('sex')
                    ->label('Jinsi')
                    ->options([
                        'male' => 'Erkak',
                        'female' => 'Ayol',
                    ]),
            ])
            ->actions([
                Action::make('download_word')
                    ->label('Word')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn ($record) => !empty($record->details['word_document']))
                    ->url(fn ($record) => route('employees.download', ['id' => $record->id]))
                    ->openUrlInNewTab(),
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
            ->emptyStateHeading('Xodimlar topilmadi')
            ->emptyStateDescription('Yangi xodim qo\'shish uchun yuqoridagi tugmani bosing')
            ->defaultSort('created_at', 'desc');
    }
}