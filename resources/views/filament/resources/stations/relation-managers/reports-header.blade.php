<div class="space-y-6 mb-6">
    <!-- Filter Tugmalari -->
    <div class="flex flex-wrap gap-3">
        <button 
            wire:click="setActiveType('yuk_ortilishi')"
            @class([
                'px-5 py-2.5 rounded-lg font-medium transition-all duration-200',
                'bg-blue-500 text-white shadow-md' => $activeType === 'yuk_ortilishi',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-600' => $activeType !== 'yuk_ortilishi',
            ])
        >
            Oylik yuk ortilishi
        </button>

        <button 
            wire:click="setActiveType('yuk_tushurilishi')"
            @class([
                'px-5 py-2.5 rounded-lg font-medium transition-all duration-200',
                'bg-yellow-500 text-white shadow-md' => $activeType === 'yuk_tushurilishi',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-yellow-50 dark:hover:bg-gray-600' => $activeType !== 'yuk_tushurilishi',
            ])
        >
            Oylik yuk tushurilishi
        </button>

        <button 
            wire:click="setActiveType('pul_tushumi')"
            @class([
                'px-5 py-2.5 rounded-lg font-medium transition-all duration-200',
                'bg-green-500 text-white shadow-md' => $activeType === 'pul_tushumi',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-600' => $activeType !== 'pul_tushumi',
            ])
        >
            Oylik tushum
        </button>

        <button 
            wire:click="setActiveType('xarajat_daromad')"
            @class([
                'px-5 py-2.5 rounded-lg font-medium transition-all duration-200',
                'bg-red-500 text-white shadow-md' => $activeType === 'xarajat_daromad',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-gray-600' => $activeType !== 'xarajat_daromad',
            ])
        >
            Xarajat va daromad
        </button>

        <button 
            wire:click="setActiveType('boshqalar')"
            @class([
                'px-5 py-2.5 rounded-lg font-medium transition-all duration-200',
                'bg-purple-500 text-white shadow-md' => $activeType === 'boshqalar',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-600' => $activeType !== 'boshqalar',
            ])
        >
            Boshqa
        </button>
    </div>

    <!-- Statistika -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Jami reja</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                {{ number_format($stats['total_planned']) }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Jami haqiqiy</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                {{ number_format($stats['total_actual']) }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Bajarilish</p>
            <p class="text-2xl font-bold mt-1 
                {{ $stats['percentage'] >= 100 ? 'text-green-600 dark:text-green-400' : ($stats['percentage'] >= 80 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                {{ $stats['percentage'] }}%
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Jami hisobotlar</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                {{ $stats['count'] }}
            </p>
        </div>
    </div>

    <!-- Diagramma -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700" wire:ignore>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Reja va Haqiqiy ko'rsatkichlar
        </h3>
        <div style="position: relative; height: 400px;">
            <canvas id="reportsChart"></canvas>
        </div>
    </div>
</div>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let chartInstance = null;

        function updateChart() {
            Livewire.find('{{ $_instance->getId() }}').call('getChartData').then(data => {
                const ctx = document.getElementById('reportsChart');
                
                if (!ctx) return;

                if (chartInstance) {
                    chartInstance.destroy();
                }

                const colors = {
                    'yuk_ortilishi': { primary: 'rgb(59, 130, 246)', secondary: 'rgba(59, 130, 246, 0.1)' },
                    'yuk_tushurilishi': { primary: 'rgb(234, 179, 8)', secondary: 'rgba(234, 179, 8, 0.1)' },
                    'pul_tushumi': { primary: 'rgb(34, 197, 94)', secondary: 'rgba(34, 197, 94, 0.1)' },
                    'xarajat_daromad': { primary: 'rgb(239, 68, 68)', secondary: 'rgba(239, 68, 68, 0.1)' },
                    'boshqalar': { primary: 'rgb(168, 85, 247)', secondary: 'rgba(168, 85, 247, 0.1)' }
                };

                const currentColor = colors[data.type] || colors['boshqalar'];

                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Reja',
                                data: data.planned,
                                borderColor: 'rgb(156, 163, 175)',
                                backgroundColor: 'rgba(156, 163, 175, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Haqiqiy',
                                data: data.actual,
                                borderColor: currentColor.primary,
                                backgroundColor: currentColor.secondary,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                },
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        }

        updateChart();

        Livewire.on('refreshChart', () => {
            setTimeout(updateChart, 100);
        });
    });
</script>
@endpush
@endonce