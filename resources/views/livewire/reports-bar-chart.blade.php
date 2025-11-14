<div>
    <canvas id="reportsBarChart" style="height:300px;"></canvas>

    <script>
        document.addEventListener('livewire:load', function () {
            const ctx = document.getElementById('reportsBarChart').getContext('2d');

            let chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Haqiqiy',
                        data: @json($data),
                        backgroundColor: '#3b82f6'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            Livewire.on('reportsBarUpdated', payload => {
                chart.data.labels = payload.labels;
                chart.data.datasets[0].data = payload.data;
                chart.update();
            });
        });
    </script>
</div>
