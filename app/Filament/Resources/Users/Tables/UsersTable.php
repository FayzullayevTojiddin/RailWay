<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Ism familiya')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'manager' => 'Menejer',
                        'operator' => 'Operator',
                        'viewer' => 'Ko\'ruvchi',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'operator' => 'info',
                        'viewer' => 'gray',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Qo\'shilgan')
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
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Admin',
                        'manager' => 'Menejer',
                        'operator' => 'Operator',
                        'viewer' => 'Ko\'ruvchi',
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
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('O\'chirish'),
                ]),
            ])
            ->defaultSort('id', 'desc')
            ->striped()
            ->emptyStateHeading('Foydalanuvchilar topilmadi')
            ->emptyStateDescription('Yangi foydalanuvchi qo\'shish uchun yuqoridagi "Yangi" tugmasini bosing.')
            ->emptyStateIcon('heroicon-o-users');
    }
}