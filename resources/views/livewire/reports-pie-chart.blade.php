<div>
    <canvas id="reportsPieChart"></canvas>

    <script>
        document.addEventListener('livewire:load', function () {
            const ctx = document.getElementById('reportsPieChart').getContext('2d');

            let chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        data: @json($data),
                        backgroundColor: ['#3b82f6', '#10b981']
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                }
            });

            Livewire.on('reportsPieUpdated', payload => {
                chart.data.labels = payload.labels;
                chart.data.datasets[0].data = payload.data;
                chart.update();
            });
        });
    </script>
</div>
