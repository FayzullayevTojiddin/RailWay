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
                    ->columnSpanFull(),

                DatePicker::make('date')
                    ->label('Sana')
                    ->required()
                    ->default(now())
                    ->native(false)
                    ->displayFormat('d.m.Y')
                    ->columnSpan(1),

                TextInput::make('planned_value')
                    ->label('Rejadagi qiymat')
                    ->required()
                    ->numeric()
                    ->suffix("dona/so'm")
                    ->default(0)
                    ->columnSpan(1),

                TextInput::make('actual_value')
                    ->label('Haqiqiy qiymat')
                    ->required()
                    ->numeric()
                    ->suffix("dona/so'm")
                    ->default(0)
                    ->columnSpan(1),

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
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'yuk_ortilishi' => 'Yuk ortilishi',
                        'yuk_tushurilishi' => 'Yuk tushurilishi',
                        'pul_tushumi' => 'Pul tushumi',
                        'xarajat_daromad' => 'Xarajat/Daromad',
                        'boshqalar' => 'Boshqalar',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match($state) {
                        'yuk_ortilishi' => 'info',
                        'yuk_tushurilishi' => 'warning',
                        'pul_tushumi' => 'success',
                        'xarajat_daromad' => 'danger',
                        'boshqalar' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Sana')
                    ->date('d.m.Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('planned_value')
                    ->label('Reja')
                    ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: '.',
                        thousandsSeparator: ' ',
                    )
                    ->sortable(),

                TextColumn::make('actual_value')
                    ->label('Haqiqiy')
                    ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: '.',
                        thousandsSeparator: ' ',
                    )
                    ->sortable(),

                TextColumn::make('percentage')
                    ->label('Bajarilish')
                    ->getStateUsing(function (Report $record): string {
                        if ($record->planned_value == 0) {
                            return '0%';
                        }
                        $percentage = ($record->actual_value / $record->planned_value) * 100;
                        return number_format($percentage, 1) . '%';
                    })
                    ->badge()
                    ->color(function (Report $record): string {
                        if ($record->planned_value == 0) {
                            return 'gray';
                        }
                        $percentage = ($record->actual_value / $record->planned_value) * 100;

                        if ($percentage >= 100) return 'success';
                        if ($percentage >= 80) return 'warning';
                        return 'danger';
                    }),

                TextColumn::make('notes')
                    ->label('Izohlar')
                    ->limit(30)
                    ->toggleable()
                    ->searchable(),
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
            ->headerActions([
                CreateAction::make()
                    ->label('Yangi hisobot')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Hisobot yaratish')
                    ->modalWidth('lg')
                    ->createAnother(false)
                    ->successNotificationTitle('Hisobot muvaffaqiyatli yaratildi'),
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

    protected function getHeaderWidgets(): array
    {
        return [
            //
        ];
    }
}
