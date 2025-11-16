<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use App\Models\Report;
use App\Filament\Resources\Stations\RelationManagers\Widgets\ReportsWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Utilities\Get;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    protected static ?string $title = 'Hisobotlar';
    protected static ?string $modelLabel = 'Hisobot';
    protected static ?string $pluralModelLabel = 'Hisobotlar';
    
    // Filter o'zgarganda table refresh bo'lishi uchun
    protected $listeners = ['updateTableFilters' => '$refresh'];

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
                    ->extraAttributes([
                        'data-flatpickr' => json_encode([
                            'plugins' => [
                                [
                                    'monthSelectPlugin',
                                    [
                                        'shorthand'  => true,
                                        'dateFormat' => 'm.Y',
                                        'altFormat'  => 'm.Y',
                                        'theme'      => 'light',
                                    ]
                                ]
                            ]
                        ])
                    ])
                    ->columnSpanFull(),

                TextInput::make('planned_value')
                    ->label('Rejadagi qiymat')
                    ->numeric()
                    ->suffix("dona/so'm")
                    ->default(null)
                    ->columnSpan(1)
                    ->visible(fn ($get) => $get('type') !== 'xarajat_daromad')
                    ->required(fn ($get) => $get('type') !== 'xarajat_daromad')
                    ->dehydrated(fn ($get) => $get('type') !== 'xarajat_daromad'),

                TextInput::make('actual_value')
                    ->label('Haqiqiy qiymat')
                    ->numeric()
                    ->suffix("dona/so'm")
                    ->default(null)
                    ->columnSpan(1)
                    ->visible(fn ($get) => $get('type') !== 'xarajat_daromad')
                    ->required(fn ($get) => $get('type') !== 'xarajat_daromad')
                    ->dehydrated(fn ($get) => $get('type') !== 'xarajat_daromad'),

                TextInput::make('expense')
                    ->label('Xarajat')
                    ->numeric()
                    ->suffix("so'm")
                    ->default(null)
                    ->columnSpan(1)
                    ->visible(fn ($get) => $get('type') === 'xarajat_daromad')
                    ->required(fn ($get) => $get('type') === 'xarajat_daromad')
                    ->dehydrated(fn ($get) => $get('type') === 'xarajat_daromad'),

                TextInput::make('income')
                    ->label('Daromad')
                    ->numeric()
                    ->suffix("so'm")
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

    protected function getSelectedType(): ?string
    {
        return $this->tableFilters['type']['value'] ?? 'yuk_ortilishi';
    }

    public function table(Table $table): Table
    {
        $selectedType = $this->getSelectedType();
        $isExpenseIncome = $selectedType === 'xarajat_daromad';

        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('date')
                    ->label('Sana')
                    ->date('d.m.Y')
                    ->sortable(),

                // Faqat xarajat/daromad EMAS bo'lsa ko'rsat
                TextColumn::make('planned_value')
                    ->label('Reja')
                    ->numeric(decimalPlaces: 0, decimalSeparator: '.', thousandsSeparator: ' ')
                    ->sortable()
                    ->visible(!$isExpenseIncome),

                TextColumn::make('actual_value')
                    ->label('Haqiqiy')
                    ->numeric(decimalPlaces: 0, decimalSeparator: '.', thousandsSeparator: ' ')
                    ->sortable()
                    ->visible(!$isExpenseIncome),

                // Faqat xarajat/daromad BO'LSA ko'rsat
                TextColumn::make('expense')
                    ->label('Xarajat')
                    ->numeric(decimalPlaces: 0, decimalSeparator: '.', thousandsSeparator: ' ')
                    ->sortable()
                    ->visible($isExpenseIncome),

                TextColumn::make('income')
                    ->label('Daromad')
                    ->numeric(decimalPlaces: 0, decimalSeparator: '.', thousandsSeparator: ' ')
                    ->sortable()
                    ->visible($isExpenseIncome),

                TextColumn::make('percentage')
                    ->label(fn () => $this->getSelectedType() === 'xarajat_daromad' ? 'Foyda' : 'Bajarilish')
                    ->getStateUsing(function ($record) use ($isExpenseIncome): string {
                        if ($isExpenseIncome) {
                            // Xarajat va daromad uchun - foyda foizini hisoblash
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
                            // Boshqa turlar uchun - bajarilish foizi
                            if (($record->planned_value ?? 0) == 0) return '0%';
                            $pct = ($record->actual_value ?? 0) / ($record->planned_value ?? 1) * 100;
                            return number_format($pct, 1) . '%';
                        }
                    })
                    ->badge()
                    ->color(function ($record) use ($isExpenseIncome): string {
                        if ($isExpenseIncome) {
                            // Xarajat va daromad uchun - foyda rangini aniqlash
                            $expense = $record->expense ?? 0;
                            $income = $record->income ?? 0;
                            
                            if ($expense == 0) {
                                return $income > 0 ? 'success' : 'gray';
                            }
                            
                            $profit = $income - $expense;
                            $profitPercent = ($profit / $expense) * 100;
                            
                            if ($profitPercent >= 50) return 'success';  // 50%+ foyda
                            if ($profitPercent >= 0) return 'warning';   // 0-50% foyda
                            if ($profitPercent >= -20) return 'danger';  // kichik zarar
                            return 'gray';                               // katta zarar
                        } else {
                            // Boshqa turlar uchun
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
                    ->default('yuk_ortilishi')
                    ->native(false),

                SelectFilter::make('month')
                    ->label('Oy')
                    ->options(fn () => $this->getLast12MonthsOptions())
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereYear('date', substr($data['value'], 0, 4))
                                  ->whereMonth('date', substr($data['value'], 5, 2));
                        }
                    })
                    ->default(now()->format('Y-m'))
                    ->native(false),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->filtersFormWidth('md')
            ->persistFiltersInSession()
            ->deferFilters(false)
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Yangi hisobot')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Hisobot yaratish')
                    ->modalWidth('lg')
                    ->createAnother(false)
                    ->successNotificationTitle('Hisobot muvaffaqiyatli yaratildi'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->label('Tahrirlash')
                    ->modalHeading('Hisobotni tahrirlash')
                    ->modalWidth('lg')
                    ->successNotificationTitle('Hisobot yangilandi')
                    ->button(),

                \Filament\Actions\DeleteAction::make()
                    ->label('O\'chirish')
                    ->modalHeading('Hisobotni o\'chirish')
                    ->modalDescription('Haqiqatan ham o\'chirmoqchimisiz?')
                    ->successNotificationTitle('Hisobot o\'chirildi')
                    ->button(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->label('O\'chirish')
                        ->modalHeading('Hisobotlarni o\'chirish')
                        ->modalDescription('Tanlangan hisobotlarni o\'chirmoqchimisiz?')
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