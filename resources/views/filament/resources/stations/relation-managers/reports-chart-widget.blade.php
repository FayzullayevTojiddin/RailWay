<x-filament::widget>
    <div class="flex gap-4">
        {{-- Chap: filter tugmalar --}}
        <div class="w-56 space-y-2">
            <h3 class="text-sm font-medium">Filterlar (tur)</h3>
            @foreach($filters as $key => $label)
                <x-filament::button
                    :color="$filterType === $key ? 'success' : 'gray'"
                    class="w-full"
                    wire:click="$set('filterType', '{{ $key }}')"
                >
                    {{ $label }}
                </x-filament::button>
            @endforeach
        </div>

        {{-- O'ng: 2 ta chart yonma-yon --}}
        <div class="grid grid-cols-2 gap-4 w-full">
            {{-- Bar chart widgetga filterType ni property sifatida yuborish --}}
            @livewire(\App\Filament\Resources\Stations\RelationManagers\Widgets\ReportsBarChart::class, ['filterType' => $filterType])
            @livewire(\App\Filament\Resources\Stations\RelationManagers\Widgets\ReportsDoughnutChart::class, ['filterType' => $filterType])
        </div>
    </div>
</x-filament::widget>