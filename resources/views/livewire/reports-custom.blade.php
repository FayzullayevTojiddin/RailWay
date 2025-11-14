{{-- resources/views/filament/resources/stations/relation-managers/reports-custom.blade.php --}}

<div>
    {{-- Header bilan birga table --}}
    <div class="fi-ta">
        <div class="fi-ta-ctn divide-y divide-gray-200 dark:divide-white/10 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            
            {{-- Header Actions --}}
            <div class="fi-ta-header-ctn px-6 py-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="fi-ta-header-heading text-base font-semibold text-gray-950 dark:text-white">
                            Hisobotlar
                        </h3>
                    </div>
                    <div class="flex items-center gap-3">
                        {{ $this->mountTableHeaderActions() }}
                    </div>
                </div>
            </div>

            {{-- Custom Content --}}
            <div class="fi-ta-content">
                @if($reports->count() > 0)
                    {{-- Statistika kartalari --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 bg-gray-50 dark:bg-gray-800/50">
                        <div class="bg-white dark:bg-gray-900 border border-blue-200 dark:border-blue-500/30 rounded-lg p-4">
                            <div class="text-blue-600 dark:text-blue-400 text-sm font-medium mb-1">Jami hisobotlar</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $reports->count() }}</div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-900 border border-green-200 dark:border-green-500/30 rounded-lg p-4">
                            <div class="text-green-600 dark:text-green-400 text-sm font-medium mb-1">Jami reja</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($reports->sum('planned_value'), 0, '.', ' ') }}
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-900 border border-purple-200 dark:border-purple-500/30 rounded-lg p-4">
                            <div class="text-purple-600 dark:text-purple-400 text-sm font-medium mb-1">Jami haqiqiy</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($reports->sum('actual_value'), 0, '.', ' ') }}
                            </div>
                        </div>
                    </div>

                    {{-- Hisobotlar ro'yxati --}}
                    <div class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($reports as $report)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        {{-- Turi va Sana --}}
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                                @switch($report->type)
                                                    @case('yuk_ortilishi')
                                                        bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30
                                                        @break
                                                    @case('yuk_tushurilishi')
                                                        bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20
                                                        @break
                                                    @case('pul_tushumi')
                                                        bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20
                                                        @break
                                                    @case('xarajat_daromad')
                                                        bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20
                                                        @break
                                                    @default
                                                        bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20
                                                @endswitch
                                            ">
                                                @switch($report->type)
                                                    @case('yuk_ortilishi')
                                                        📦 Yuk ortilishi
                                                        @break
                                                    @case('yuk_tushurilishi')
                                                        📤 Yuk tushurilishi
                                                        @break
                                                    @case('pul_tushumi')
                                                        💰 Pul tushumi
                                                        @break
                                                    @case('xarajat_daromad')
                                                        📊 Xarajat/Daromad
                                                        @break
                                                    @default
                                                        📋 Boshqalar
                                                @endswitch
                                            </span>
                                            
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $report->date->format('d.m.Y') }}
                                            </span>
                                        </div>
                                        
                                        {{-- Ma'lumotlar --}}
                                        <div class="grid grid-cols-3 gap-6">
                                            <div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Reja</div>
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ number_format($report->planned_value, 0, '.', ' ') }}
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Haqiqiy</div>
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ number_format($report->actual_value, 0, '.', ' ') }}
                                                </div>
                                            </div>
                                            
                                            <div>
                                                @php
                                                    $percentage = $report->planned_value > 0 
                                                        ? ($report->actual_value / $report->planned_value) * 100 
                                                        : 0;
                                                @endphp
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bajarilish</div>
                                                <div class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-semibold
                                                    @if($percentage >= 100)
                                                        bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20
                                                    @elseif($percentage >= 80)
                                                        bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20
                                                    @else
                                                        bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20
                                                    @endif
                                                ">
                                                    {{ number_format($percentage, 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Izohlar --}}
                                        @if($report->notes)
                                            <div class="mt-3 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-md px-3 py-2">
                                                {{ $report->notes }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1">
                                        {{ ($this->mountTableAction('edit', $report->id)) }}
                                        {{ ($this->mountTableAction('delete', $report->id)) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Bo'sh holat --}}
                    <div class="fi-ta-empty-state px-6 py-12">
                        <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                            <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                                <svg class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            
                            <h4 class="fi-ta-empty-state-heading text-base font-semibold text-gray-950 dark:text-white">
                                Hisobotlar topilmadi
                            </h4>
                            
                            <p class="fi-ta-empty-state-description text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Yangi hisobot yaratish uchun yuqoridagi tugmani bosing
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Filament table actions uchun --}}
    <x-filament-actions::modals />
</div>