<div class="status-chart">
    <h2>Meter Status</h2>
    <canvas id="statusChart"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('statusChart').getContext('2d');
        var statusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Online', 'Offline'],
            datasets: [{
                label: 'Meter Status',
                data: [10, 5], // placeholder
                backgroundColor: ['#36a2eb', '#ff6384'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += context.parsed;
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