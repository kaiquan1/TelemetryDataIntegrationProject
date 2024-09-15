<?php
// water_consumption.php

include 'db_connection.php';

function getSiteLabels($conn) {
    $siteLabels = [];
    $siteQuery = "SELECT DISTINCT STA_Label FROM [ScadaNetDb].[dbo].[View_Stations]";
    $siteStmt = sqlsrv_query($conn, $siteQuery);

    if ($siteStmt === false) {
        die("Error fetching site labels.");
    }

    while ($row = sqlsrv_fetch_array($siteStmt, SQLSRV_FETCH_ASSOC)) {
        $siteLabels[] = $row['STA_Label'];
    }

    sqlsrv_free_stmt($siteStmt);
    return $siteLabels;
}

$siteLabels = getSiteLabels($conn);
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .water-consumption {
            padding: 20px;
            border: 1px solid #ccc;
            margin: 20px;
            position: relative;
        }
        .form1 {
            margin-bottom: 20px;
        }
        label {
            margin-right: 10px;
        }
        input, select {
            margin-right: 20px;
        }
        #okButton {
            padding: 5px 10px;
        }

        #loadingIndicator {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000; 
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #36a2eb;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="water-consumption">
        <h2>Water Consumption</h2>
        <div class="form1">
            <form id="waterForm">
                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" name="startDate" required>
                
                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" name="endDate" required>
                
                <br><br>
                
                <label for="INF_VALUE">Metering:</label>
                <select name="INF_VALUE" id="INF_VALUE" required>
                    <option value="metering1">Metering 1</option>
                    <option value="metering2">Metering 2</option>
                    <option value="metering3">Metering 3</option>
                </select>
                
                <label for="STA_LABEL">Site:</label>
                <select name="STA_LABEL" id="STA_LABEL" required>
                    <?php
                    // Dynamically populate site options from the database
                    foreach ($siteLabels as $siteLabel) {
                        echo "<option value=\"$siteLabel\">$siteLabel</option>";
                    }
                    ?>
                </select>
                
                <br><br>
                
                <button type="submit" id="okButton">OK</button>
            </form>
        </div>
        
        <!-- Loading Indicator -->
        <div id="loadingIndicator">
            <div class="spinner"></div>
            <div>Loading...</div>
        </div>

        <canvas id="waterChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('waterChart').getContext('2d');
        let waterConsumptionChart;

        // Function to initialize the chart
        function initializeChart(data) {
            const labels = data.map(item => item.x);
            const dataValues = data.map(item => item.y);

            waterConsumptionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels, // Dates
                    datasets: [{
                        label: 'Water Consumption (m³)',
                        data: dataValues, // Metering values
                        borderColor: '#36a2eb',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
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
                            beginAtZero: false,
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
        }

        // Function to update the chart with new data
        function updateChart(data) {
            const labels = data.map(item => item.x);
            const dataValues = data.map(item => item.y);
            waterConsumptionChart.data.labels = labels;
            waterConsumptionChart.data.datasets[0].data = dataValues;
            waterConsumptionChart.update();
        }

        // Function to show the loading indicator
        function showLoading() {
            document.getElementById('loadingIndicator').style.display = 'block';
        }

        // Function to hide the loading indicator
        function hideLoading() {
            document.getElementById('loadingIndicator').style.display = 'none';
        }

        initializeChart([]);

        const form = document.getElementById('waterForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            showLoading();

            const formData = new FormData(form);
            const params = new URLSearchParams();
            for (const pair of formData) {
                params.append(pair[0], pair[1]);
            }

            fetch('water_consumption_data.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: params
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();

                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }
                updateChart(data);
            })
            .catch(error => {
                hideLoading();

                console.error('Error:', error);
                alert('An error occurred while fetching data.');
            });
        });
    });
</script>
</body>
</html>
