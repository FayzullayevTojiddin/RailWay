<div class="space-y-4">
    <x-filament-widgets::widgets
        :widgets="[
            \App\Filament\Resources\Stations\RelationManagers\Widgets\ReportsStatsWidget::class,
            \App\Filament\Resources\Stations\RelationManagers\Widgets\ReportsPieChartWidget::class,
            \App\Filament\Resources\Stations\RelationManagers\Widgets\ReportsBarChartWidget::class,
        ]"
    />

    {{-- Jadvalni RelationManager orqali render qilamiz --}}
    {{ $this->table() }}
</div>