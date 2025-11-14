@php
    $types = [
        'yuk_ortilishi' => ['icon' => '📦', 'label' => 'Yuk ortilishi'],
        'yuk_tushurilishi' => ['icon' => '📤', 'label' => 'Yuk tushurilishi'],
        'pul_tushumi' => ['icon' => '💰', 'label' => 'Pul tushumi'],
        'xarajat_daromad' => ['icon' => '📊', 'label' => 'Xarajat/Daromad'],
        'boshqalar' => ['icon' => '📋', 'label' => 'Boshqalar'],
    ];
    
    $chartData = $this->getChartData();
@endphp

<x-filament-widgets::widget>
    <div class="space-y-6">
        {{-- Filter Buttons --}}
        <div class="flex flex-wrap gap-2">
            @foreach($types as $key => $type)
                <button 
                    wire:click="selectType('{{ $key }}')"
                    @class([
                        'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all',
                        'bg-primary-600 text-white shadow-sm' => $selectedType === $key,
                        'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' => $selectedType !== $key,
                    ])
                >
                    <span>{{ $type['icon'] }}</span>
                    <span>{{ $type['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Doughnut Chart --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Aylana diagramma
                    </h3>
                </div>
                <div class="p-4">
                    <canvas 
                        wire:ignore
                        id="doughnutChart_{{ $this->getId() }}"
                        style="height: 300px;"
                    ></canvas>
                </div>
            </div>

            {{-- Bar Chart --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Ustunli diagramma
                    </h3>
                </div>
                <div class="p-4">
                    <canvas 
                        wire:ignore
                        id="barChart_{{ $this->getId() }}"
                        style="height: 300px;"
                    ></canvas>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            let doughnutChart = null;
            let barChart = null;
            const widgetId = '{{ $this->getId() }}';

            function createCharts() {
                const doughnutCanvas = document.getElementById(`doughnutChart_${widgetId}`);
                const barCanvas = document.getElementById(`barChart_${widgetId}`);

                if (!doughnutCanvas || !barCanvas) {
                    setTimeout(createCharts, 100);
                    return;
                }

                // Destroy existing charts
                if (doughnutChart) doughnutChart.destroy();
                if (barChart) barChart.destroy();

                // Doughnut Chart
                doughnutChart = new Chart(doughnutCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Reja', 'Haqiqiy'],
                        datasets: [{
                            data: [
                                {{ $chartData['doughnut']['planned'] }},
                                {{ $chartData['doughnut']['actual'] }}
                            ],
                            backgroundColor: ['#10b981', '#3b82f6'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                });

                // Bar Chart
                barChart = new Chart(barCanvas, {
                    type: 'bar',
                    data: {
                        labels: @json($chartData['bar']['labels']),
                        datasets: [
                            {
                                label: 'Reja',
                                data: @json($chartData['bar']['planned']),
                                backgroundColor: '#10b981',
                            },
                            {
                                label: 'Haqiqiy',
                                data: @json($chartData['bar']['actual']),
                                backgroundColor: '#3b82f6',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            createCharts();

            // Livewire yangilanishlarini tinglash
            Livewire.on('$refresh', () => {
                setTimeout(createCharts, 100);
            });
        });
    </script>
</x-filament-widgets::widget>