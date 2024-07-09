<div class="water-consumption">
    <h2>Water Consumption</h2>
    <canvas id="waterChart"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('waterChart').getContext('2d');
        var waterConsumptionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['2024-07-01', '2024-07-02', '2024-07-03', '2024-07-04', '2024-07-05', '2024-07-06', '2024-07-07'], // Placeholder dates
                datasets: [{
                    label: 'Water Consumption (m³)',
                    data: [2.5, 3.0, 2.8, 3.2, 3.5, 3.0, 3.3], // Placeholder data
                    borderColor: '#36a2eb',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Water Consumption (m³)'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' m³';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</div>