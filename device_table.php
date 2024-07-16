<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
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

                tr.innerHTML = `
                    <td>${row.STA_Label ?? ''}</td>
                    <td>${(row.Metering1 ?? 0).toFixed(2)}</td>
                    <td>${(row.Metering2 ?? 0).toFixed(2)}</td>
                    <td>${(row.Metering3 ?? 0).toFixed(2)}</td>
                    <td>${(row.Flowrate ?? 0).toFixed(2)}</td>
                    <td>${row.MeterBattery ?? 0}%</td>
                    <td>${row.LoggerBattery ?? 0}</td>
                    <td>${row.LastUpdated ?? ''}</td>
                `;

                tableBody.appendChild(tr);
            });
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
