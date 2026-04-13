<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use App\Models\Report;
use Filament\Actions\Action;
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
use Illuminate\Contracts\View\View;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';
    protected static ?string $title = 'Hisobotlar';
    protected static ?string $modelLabel = 'Hisobot';
    protected static ?string $pluralModelLabel = 'Hisobotlar';

    public string $cmpType = 'yuk_ortilishi';
    public string $cmpMonth1 = '';
    public string $cmpMonth2 = '';
    public array $cmpResult = [];
    public bool $cmpShowResults = false;
    public array $cmpAvailableMonths = [];

    public function isReadOnly(): bool
    {
        return false;
    }

    public function mountComparison(): void
    {
        $this->cmpResult = [];
        $this->cmpShowResults = false;
        $this->cmpMonth1 = '';
        $this->cmpMonth2 = '';
        $this->loadAvailableMonths();
    }

    public function updatedCmpType(): void
    {
        $this->cmpMonth1 = '';
        $this->cmpMonth2 = '';
        $this->cmpResult = [];
        $this->cmpShowResults = false;
        $this->loadAvailableMonths();
    }

    public function loadAvailableMonths(): void
    {
        $stationId = $this->getOwnerRecord()->id;
        $monthNames = [1=>'Yanvar',2=>'Fevral',3=>'Mart',4=>'Aprel',5=>'May',6=>'Iyun',7=>'Iyul',8=>'Avgust',9=>'Sentabr',10=>'Oktabr',11=>'Noyabr',12=>'Dekabr'];

        $dates = Report::where('station_id', $stationId)
            ->where('type', $this->cmpType)
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m'))
            ->unique()
            ->values();

        $this->cmpAvailableMonths = [];
        foreach ($dates as $ym) {
            $parts = explode('-', $ym);
            $this->cmpAvailableMonths[$ym] = $monthNames[(int)$parts[1]] . ' ' . $parts[0];
        }

        if ($dates->count() >= 2) {
            $this->cmpMonth1 = $dates[1];
            $this->cmpMonth2 = $dates[0];
        } elseif ($dates->count() === 1) {
            $this->cmpMonth1 = $dates[0];
            $this->cmpMonth2 = $dates[0];
        }
    }

    public function runComparison(): void
    {
        if (!$this->cmpMonth1 || !$this->cmpMonth2) {
            return;
        }

        $stationId = $this->getOwnerRecord()->id;

        $report1 = Report::where('station_id', $stationId)
            ->where('type', $this->cmpType)
            ->whereYear('date', substr($this->cmpMonth1, 0, 4))
            ->whereMonth('date', substr($this->cmpMonth1, 5, 2))
            ->first();

        $report2 = Report::where('station_id', $stationId)
            ->where('type', $this->cmpType)
            ->whereYear('date', substr($this->cmpMonth2, 0, 4))
            ->whereMonth('date', substr($this->cmpMonth2, 5, 2))
            ->first();

        $pct = function ($v1, $v2) {
            if ($v1 == 0) return $v2 > 0 ? '+100%' : '0%';
            $p = (($v2 - $v1) / $v1) * 100;
            return ($p >= 0 ? '+' : '') . number_format($p, 1) . '%';
        };

        if ($this->cmpType === 'xarajat_daromad') {
            $e1 = $report1->expense ?? 0; $e2 = $report2->expense ?? 0;
            $i1 = $report1->income ?? 0; $i2 = $report2->income ?? 0;
            $this->cmpResult = [
                ['label' => 'Xarajat', 'v1' => $e1, 'v2' => $e2, 'pct' => $pct($e1, $e2)],
                ['label' => 'Daromad', 'v1' => $i1, 'v2' => $i2, 'pct' => $pct($i1, $i2)],
            ];
        } else {
            $p1 = $report1->planned_value ?? 0; $p2 = $report2->planned_value ?? 0;
            $a1 = $report1->actual_value ?? 0; $a2 = $report2->actual_value ?? 0;
            $this->cmpResult = [
                ['label' => 'Reja', 'v1' => $p1, 'v2' => $p2, 'pct' => $pct($p1, $p2)],
                ['label' => 'Haqiqiy', 'v1' => $a1, 'v2' => $a2, 'pct' => $pct($a1, $a2)],
            ];
        }

        $this->cmpShowResults = true;
    }

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
                TextColumn::make('date')
                    ->label('Sana')
                    ->date('m.Y')
                    ->sortable(),

                ViewColumn::make('chart')
                    ->label('Diagramma')
                    ->view('filament.resources.stations.relation-managers.columns.report-chart'),

                TextColumn::make('planned_value')
                    ->label('Reja')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        $formatted = number_format($state, 0, '.', ' ');
                        if ($record->type === 'xarajat_daromad') {
                            return $formatted . ' so\'m';
                        }
                        return $formatted . ' dona/vagon';
                    })
                    ->sortable(),

                TextColumn::make('actual_value')
                    ->label('Haqiqiy')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        $formatted = number_format($state, 0, '.', ' ');
                        if ($record->type === 'xarajat_daromad') {
                            return $formatted . ' so\'m';
                        }
                        return $formatted . ' dona/vagon';
                    })
                    ->sortable(),

                TextColumn::make('expense')
                    ->label('Xarajat')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '.', ' ') . ' so\'m' : '-')
                    ->sortable(),

                TextColumn::make('income')
                    ->label('Daromad')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, '.', ' ') . ' so\'m' : '-')
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
                Action::make('compare')
                    ->label('Taqqoslash')
                    ->icon('heroicon-o-scale')
                    ->color('info')
                    ->modalHeading('Oylik hisobotlarni taqqoslash')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Yopish')
                    ->mountUsing(fn () => $this->mountComparison())
                    ->modalContent(fn () => view('filament.modals.reports-comparison', [
                        'monthOptions' => $this->cmpAvailableMonths,
                        'cmpType' => $this->cmpType,
                        'cmpMonth1' => $this->cmpMonth1,
                        'cmpMonth2' => $this->cmpMonth2,
                        'cmpResult' => $this->cmpResult,
                        'cmpShowResults' => $this->cmpShowResults,
                    ])),
                CreateAction::make()
                    ->label('Yangi hisobot')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Hisobot yaratish')
                    ->modalWidth('lg')
                    ->createAnother(false)
                    ->successNotificationTitle('Hisobot yaratildi'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Hisobotni tahrirlash')
                    ->modalWidth('lg')
                    ->successNotificationTitle('Hisobot yangilandi')
                    ->iconButton(),

                DeleteAction::make()
                    ->modalHeading('Hisobotni o\'chirish')
                    ->modalDescription('Haqiqatan ham o\'chirmoqchimisiz?')
                    ->successNotificationTitle('Hisobot o\'chirildi')
                    ->iconButton(),
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