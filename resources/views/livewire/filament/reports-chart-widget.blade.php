<div class="filament-widget">
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <h3 class="text-sm font-medium mb-2">Hisobot diagrammasi</h3>

        <div wire:ignore>
            <canvas id="reportsChart-{{ md5(json_encode($labels)) }}" height="200"></canvas>
        </div>
    </div>
</div>

@pushOnce('scripts')
    {{-- Chart.js CDN (agar loyihada bundllangan bo'lsa, uni o'rniga qo'ying) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endPushOnce

@push('scripts')
<script>
    (function () {
        const labels = @json($labels);
        const planned = @json($planned);
        const actual = @json($actual);
        const ctxId = 'reportsChart-{{ md5(json_encode($labels)) }}';
        const ctx = document.getElementById(ctxId);

        if (! ctx) return;

        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Reja',
                        data: planned,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Haqiqiy',
                        data: actual,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    })();
</script>
@endpush