<?php

namespace App\Filament\Resources\Stations\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

// 1. AYLANA DIAGRAMMA - Hisobot turlari bo'yicha
class ReportsPieChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Hisobotlar taqsimoti';
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
        // Tanlangan oy uchun ma'lumotlar
        $currentMonth = now()->format('Y-m');
        
        $data = Report::query()
/*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Load data for the pie chart based on the given filters.
     *
     * @param array $filters
     * @return void
     */
/*******  c01ef52b-9e4f-495d-a5af-37407cbe81ce  *******/            ->whereYear('date', substr($currentMonth, 0, 4))
            ->whereMonth('date', substr($currentMonth, 5, 2))
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        $labels = [];
        $values = [];
        $colors = [
            'yuk_ortilishi' => '#3b82f6',      // blue
            'yuk_tushurilishi' => '#f59e0b',   // amber
            'pul_tushumi' => '#10b981',         // green
            'xarajat_daromad' => '#ef4444',     // red
            'boshqalar' => '#6b7280',           // gray
        ];

        foreach ($data as $item) {
            $labels[] = match($item->type) {
                'yuk_ortilishi' => 'Yuk ortilishi',
                'yuk_tushurilishi' => 'Yuk tushurilishi',
                'pul_tushumi' => 'Pul tushumi',
                'xarajat_daromad' => 'Xarajat/Daromad',
                'boshqalar' => 'Boshqalar',
                default => $item->type,
            };
            $values[] = $item->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hisobotlar soni',
                    'data' => $values,
                    'backgroundColor' => array_values($colors),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

// 2. USTUNLI DIAGRAMMA - Reja va Haqiqiy qiymatlar
class ReportsBarChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Reja vs Haqiqiy ko\'rsatkichlar';
    protected static ?int $sort = 3;
    
    protected function getData(): array
    {
        // Tanlangan oy uchun ma'lumotlar
        $currentMonth = now()->format('Y-m');
        
        $data = Report::query()
            ->whereYear('date', substr($currentMonth, 0, 4))
            ->whereMonth('date', substr($currentMonth, 5, 2))
            ->select('type', DB::raw('SUM(planned_value) as total_planned'), DB::raw('SUM(actual_value) as total_actual'))
            ->groupBy('type')
            ->get();

        $labels = [];
        $plannedValues = [];
        $actualValues = [];

        foreach ($data as $item) {
            $labels[] = match($item->type) {
                'yuk_ortilishi' => 'Yuk ortilishi',
                'yuk_tushurilishi' => 'Yuk tushurilishi',
                'pul_tushumi' => 'Pul tushumi',
                'xarajat_daromad' => 'Xarajat/Daromad',
                'boshqalar' => 'Boshqalar',
                default => $item->type,
            };
            $plannedValues[] = $item->total_planned ?? 0;
            $actualValues[] = $item->total_actual ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Reja',
                    'data' => $plannedValues,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Haqiqiy',
                    'data' => $actualValues,
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}

// 3. STATISTIKA WIDGETI
class ReportsStatsWidget extends \Filament\Widgets\StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $currentMonth = now()->format('Y-m');
        
        $totalReports = Report::query()
            ->whereYear('date', substr($currentMonth, 0, 4))
            ->whereMonth('date', substr($currentMonth, 5, 2))
            ->count();

        $totalPlanned = Report::query()
            ->whereYear('date', substr($currentMonth, 0, 4))
            ->whereMonth('date', substr($currentMonth, 5, 2))
            ->sum('planned_value');

        $totalActual = Report::query()
            ->whereYear('date', substr($currentMonth, 0, 4))
            ->whereMonth('date', substr($currentMonth, 5, 2))
            ->sum('actual_value');

        $percentage = $totalPlanned > 0 ? round(($totalActual / $totalPlanned) * 100, 1) : 0;

        return [
            \Filament\Widgets\StatsOverviewWidget\Stat::make('Jami hisobotlar', $totalReports)
                ->description('Oylik hisobotlar soni')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('Reja', number_format($totalPlanned, 0, '.', ' '))
                ->description('Rejalashtirilgan')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('warning'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('Haqiqiy', number_format($totalActual, 0, '.', ' '))
                ->description($percentage . '% bajarilgan')
                ->descriptionIcon($percentage >= 100 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($percentage >= 100 ? 'success' : ($percentage >= 80 ? 'warning' : 'danger')),
        ];
    }
}