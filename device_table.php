<!DOCTYPE html>
<html>
<head>
    <style>
        .battery-container {
            width: 40px;
            height: 15px;
            border: 2px solid #000;
            border-radius: 3px;
            position: relative;
            display: inline-block;
            margin-left: 5px;
        }
        
        .battery-level {
            height: 100%;
            background-color: green;
        }
        
        .battery-head {
            width: 3px;
            height: 6px;
            background-color: #000;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: -5px;
        }
        
        .low-battery {
            background-color: red;
        }
        
        .medium-battery {
            background-color: orange;
        }
        
        .high-battery {
            background-color: green;
        }
    </style>
    <script>
        async function fetchData() {
            try {
                const response = await fetch('fetch_table_data.php');
                const data = await response.json();
                updateTable(data);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function updateTable(data) {
            const tableBody = document.getElementById('data-body');
            tableBody.innerHTML = '';

            data.forEach(row => {
                const tr = document.createElement('tr');

                const batteryPercentage = row.MeterBattery ?? 0;

                tr.innerHTML = `
                    <td>${row.STA_Label ?? ''}</td>
                    <td>${(row.Metering1 ?? 0).toFixed(2)}</td>
                    <td>${(row.Metering2 ?? 0).toFixed(2)}</td>
                    <td>${(row.Metering3 ?? 0).toFixed(2)}</td>
                    <td>${(row.Flowrate ?? 0).toFixed(2)}</td>
                    <td>
                        ${batteryPercentage}%
                        <div class="battery-container">
                            <div class="battery-level ${getBatteryClass(batteryPercentage)}" style="width: ${batteryPercentage}%;"></div>
                            <div class="battery-head"></div>
                        </div>
                    </td>
                    <td>${row.LoggerBattery ?? 0}</td>
                    <td>${row.LastUpdated ?? ''}</td>
                `;

                tableBody.appendChild(tr);
            });
        }

        function getBatteryClass(batteryPercentage) {
            if (batteryPercentage <= 20) {
                return 'low-battery';
            } else if (batteryPercentage <= 50) {
                return 'medium-battery';
            } else {
                return 'high-battery';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchData();
            setInterval(fetchData, 10000);
        });
    </script>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Site Number</th>
                <th>Metering 1 (m<sup>3</sup>)</th>
                <th>Metering 2 (m<sup>3</sup>)</th>
                <th>Metering 3 (m<sup>3</sup>)</th>
                <th>Flowrate (m<sup>3</sup>/h)</th>
                <th>Meter Remaining Battery</th>
                <th>Logger Remaining Battery (Days)</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody id="data-body">
            <!-- Data will be injected here by JavaScript -->
        </tbody>
    </table>
</body>
</html>
