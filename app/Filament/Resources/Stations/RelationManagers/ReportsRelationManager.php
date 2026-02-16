<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use App\Models\Report;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';
    protected static ?string $title = 'Hisobotlar';
    protected static ?string $modelLabel = 'Hisobot';
    protected static ?string $pluralModelLabel = 'Hisobotlar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Hisobot turi')
                    ->required()
                    ->options([
                        'yuk_ortilishi' => '📦 Oylik yuk ortilishi',
                        'yuk_tushurilishi' => '📤 Oylik yuk tushurilishi',
                        'pul_tushumi' => '💰 Oylik pul tushumi',
                        'xarajat_daromad' => '📊 Oylik xarajat va daromad',
                        'boshqalar' => '📋 Boshqalar',
                    ])
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state === 'xarajat_daromad') {
                            $set('planned_value', null);
                            $set('actual_value', null);
                        } else {
                            $set('expense', null);
                            $set('income', null);
                        }
                    })
                    ->columnSpanFull(),

                DatePicker::make('date')
                    ->label('Sana')
                    ->native(false)
                    ->required()
                    ->displayFormat('m.Y')
                    ->format('Y-m-01')
                    ->default(now()->startOfMonth())
                    ->columnSpanFull(),

                TextInput::make('planned_value')
                    ->label('Rejadagi qiymat')
                    ->numeric()
                    ->default(null)
                    ->columnSpan(1)
                    ->visible(fn ($get) => $get('type') !== 'xarajat_daromad')
                    ->required(fn ($get) => $get('type') !== 'xarajat_daromad')
                    ->dehydrated(fn ($get) => $get('type') !== 'xarajat_daromad'),

                TextInput::make('actual_value')
                    ->label('Haqiqiy qiymat')
                    ->numeric()
                    ->default(null)
                    ->columnSpan(1)
                    ->visible(fn ($get) => $get('type') !== 'xarajat_daromad')
                    ->required(fn ($get) => $get('type') !== 'xarajat_daromad')
                    ->dehydrated(fn ($get) => $get('type') !== 'xarajat_daromad'),

                TextInput::make('expense')
                    ->label('Xarajat')
                    ->numeric()
                    ->default(null)
                    ->columnSpan(1)
                    ->visible(fn ($get) => $get('type') === 'xarajat_daromad')
                    ->required(fn ($get) => $get('type') === 'xarajat_daromad')
                    ->dehydrated(fn ($get) => $get('type') === 'xarajat_daromad'),

                TextInput::make('income')
                    ->label('Daromad')
                    ->numeric()
                    ->default(null)
                    ->columnSpan(1)
                    ->visible(fn ($get) => $get('type') === 'xarajat_daromad')
                    ->required(fn ($get) => $get('type') === 'xarajat_daromad')
                    ->dehydrated(fn ($get) => $get('type') === 'xarajat_daromad'),

                Textarea::make('notes')
                    ->label('Izohlar')
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder("Qo'shimcha ma'lumotlar...")
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('type')
                    ->label('Turi')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'yuk_ortilishi'   => '📦 Oylik yuk ortilishi',
                        'yuk_tushurilishi'=> '📤 Oylik yuk tushurilishi',
                        'pul_tushumi'    => '💰 Oylik pul tushumi',
                        'xarajat_daromad'=> '📊 Oylik xarajat va daromad',
                        default          => '📋 Boshqalar',
                    }),
                    
                TextColumn::make('date')
                    ->label('Sana')
                    ->date('d.m.Y')
                    ->sortable(),

                ViewColumn::make('chart')
                    ->label('Grafik')
                    ->view('filament.tables.columns.report-chart')
                    ->alignCenter()
                    ->disableClick()
                    ->extraCellAttributes(['style' => 'height: 100px; vertical-align: middle;'])
                    ->sortable(false),

                TextColumn::make('planned_value')
                    ->label('Reja')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        $formatted = number_format($state, 0, '.', ' ');
                        $unit = match($record->type) {
                            'pul_tushumi' => " so'm",
                            'yuk_ortilishi', 'yuk_tushurilishi' => ' dona / vagon',
                            default => ''
                        };
                        return $formatted . $unit;
                    })
                    ->sortable(),

                TextColumn::make('actual_value')
                    ->label('Haqiqiy')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        $formatted = number_format($state, 0, '.', ' ');
                        $unit = match($record->type) {
                            'pul_tushumi' => " so'm",
                            'yuk_ortilishi', 'yuk_tushurilishi' => ' dona / vagon',
                            default => ''
                        };
                        return $formatted . $unit;
                    })
                    ->sortable(),

                TextColumn::make('expense')
                    ->label('Xarajat')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '.', ' ') . " so'm" : '-')
                    ->sortable(),

                TextColumn::make('income')
                    ->label('Daromad')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '.', ' ') . " so'm" : '-')
                    ->sortable(),

                TextColumn::make('percentage')
                    ->label('Natija')
                    ->getStateUsing(function ($record): string {
                        if ($record->type === 'xarajat_daromad') {
                            $expense = $record->expense ?? 0;
                            $income = $record->income ?? 0;
                            
                            if ($expense == 0) {
                                return $income > 0 ? '+100%' : '0%';
                            }
                            
                            $profit = $income - $expense;
                            $profitPercent = ($profit / $expense) * 100;
                            
                            $sign = $profitPercent >= 0 ? '+' : '';
                            return $sign . number_format($profitPercent, 1) . '%';
                        } else {
                            if (($record->planned_value ?? 0) == 0) return '0%';
                            $pct = ($record->actual_value ?? 0) / ($record->planned_value ?? 1) * 100;
                            return number_format($pct, 1) . '%';
                        }
                    })
                    ->badge()
                    ->color(function ($record): string {
                        if ($record->type === 'xarajat_daromad') {
                            $expense = $record->expense ?? 0;
                            $income = $record->income ?? 0;
                            
                            if ($expense == 0) {
                                return $income > 0 ? 'success' : 'gray';
                            }
                            
                            $profit = $income - $expense;
                            $profitPercent = ($profit / $expense) * 100;
                            
                            if ($profitPercent >= 50) return 'success';
                            if ($profitPercent >= 0) return 'warning';
                            if ($profitPercent >= -20) return 'danger';
                            return 'gray';
                        } else {
                            if (($record->planned_value ?? 0) == 0) return 'gray';
                            $pct = ($record->actual_value ?? 0) / ($record->planned_value ?? 1) * 100;
                            if ($pct >= 100) return 'success';
                            if ($pct >= 80) return 'warning';
                            return 'danger';
                        }
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Hisobot turi')
                    ->options([
                        'yuk_ortilishi' => '📦 Oylik yuk ortilishi',
                        'yuk_tushurilishi' => '📤 Oylik yuk tushurilishi',
                        'pul_tushumi' => '💰 Oylik pul tushumi',
                        'xarajat_daromad' => '📊 Oylik xarajat va daromad',
                        'boshqalar' => '📋 Boshqalar',
                    ])
                    ->native(false),

                Filter::make('date_from')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Dan')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['date_from'] ?? null) {
                            return ['Dan: ' . Carbon::parse($data['date_from'])->format('d.m.Y')];
                        }
                        return [];
                    }),

                Filter::make('date_until')
                    ->form([
                        DatePicker::make('date_until')
                            ->label('Gacha')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['date_until'] ?? null) {
                            return ['Gacha: ' . Carbon::parse($data['date_until'])->format('d.m.Y')];
                        }
                        return [];
                    }),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->deferFilters(false)
            ->headerActions([
                CreateAction::make()
                    ->label('Yangi hisobot')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Hisobot yaratish')
                    ->modalWidth('lg')
                    ->createAnother(false)
                    ->successNotificationTitle('Hisobot yaratildi'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Tahrirlash')
                    ->modalHeading('Hisobotni tahrirlash')
                    ->modalWidth('lg')
                    ->successNotificationTitle('Hisobot yangilandi')
                    ->button(),

                DeleteAction::make()
                    ->label('O\'chirish')
                    ->modalHeading('Hisobotni o\'chirish')
                    ->modalDescription('Haqiqatan ham o\'chirmoqchimisiz?')
                    ->successNotificationTitle('Hisobot o\'chirildi')
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('O\'chirish')
                        ->modalHeading('Hisobotlarni o\'chirish')
                        ->successNotificationTitle('Hisobotlar o\'chirildi'),
                ]),
            ])
            ->emptyStateHeading('Hisobotlar topilmadi')
            ->emptyStateDescription('Yangi hisobot yaratish uchun yuqoridagi tugmani bosing')
            ->emptyStateIcon('heroicon-o-document-chart-bar');
    }

    protected function getLast12MonthsOptions(): array
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->locale('uz_Latn')->isoFormat('MMMM YYYY');
        }
        return $months;
    }
}