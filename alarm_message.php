<div class="alarm-message">
    <h2>Alarm Messages</h2>
    <div class="header-with-arrows">
        <span class="arrow-left" onclick="changeDataType(-1)">&#8592;</span>
        <h3 id="data-type-header">Low Metering 1</h2>
        <span class="arrow-right" onclick="changeDataType(1)">&#8594;</span>
    </div>

    <style>
        .header-with-arrows {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .generic-data-table {
            margin-left: auto;
            margin-right: auto;
            margin-top: 15px;
            margin-bottom: 10px;
            text-align: left;
            line-height: 25px;
            width: 80%;
            border-collapse: collapse;
        }

        .generic-data-table th, .generic-data-table td {
            border: 1px solid black;
            padding: 2px;
            padding-left: 5px;
            text-align: left;
        }

        .generic-data-table th {
            background-color: #f2f2f2;
        }

        .generic-data-table td {
            color: red;
        }

        .arrow-left, .arrow-right {
            cursor: pointer;
            font-size: 20px;
            color: black;
            border: 1px solid black;
            padding: 5px;
            border-radius: 10px;
        }
    </style>

    <form id="thresholdForm">
        <label for="threshold">Set Low Threshold:</label>
        <input type="number" id="threshold" name="threshold" value="50000.0">
        <button type="button" onclick="fetchAndDisplayData()">Apply</button>
    </form>

    <table id="generic-data-table" class="generic-data-table">
        <thead>
            <tr>
                <th>Site Number</th>
                <th id="data-type-column">Metering 1 (m<sup>3</sup>)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Table rows will be added here -->
        </tbody>
    </table>

    <script>
        const dataTypes = [
            { name: 'Metering 1', column: 'Metering1', defaultThreshold: 50000, step: 10000, unit: '(m<sup>3</sup>)' },
            { name: 'Metering 2', column: 'Metering2', defaultThreshold: 50, step: 10, unit: '(m<sup>3</sup>)' },
            { name: 'Metering 3', column: 'Metering3', defaultThreshold: 100000, step: 100000, unit: '(m<sup>3</sup>)' },
            { name: 'Flowrate', column: 'Flowrate', defaultThreshold: 10, step: 10, unit: '(m<sup>3</sup>/h)' },
            { name: 'Meter Remaining Battery', column: 'MeterBattery', defaultThreshold: 20, step: 5, unit: '(%)' },
            { name: 'Logger Remaining Battery', column: 'LoggerBattery', defaultThreshold: 10, step: 5, unit: '(Days)' },
        ];

        let currentDataTypeIndex = 0;

        function updateHeaderAndThreshold() {
            const dataType = dataTypes[currentDataTypeIndex];
            document.getElementById('data-type-header').textContent = `Low ${dataType.name}`;
            document.getElementById('data-type-column').innerHTML = `${dataType.name} ${dataType.unit}`;
            document.getElementById('threshold').value = dataType.defaultThreshold;
            document.getElementById('threshold').step = dataType.step;
            fetchAndDisplayData();
        }

        function changeDataType(direction) {
            currentDataTypeIndex = (currentDataTypeIndex + direction + dataTypes.length) % dataTypes.length;
            updateHeaderAndThreshold();
        }

        async function fetchAndDisplayData() {
            try {
                const response = await fetch('fetch_table_data.php');
                const data = await response.json();
                const threshold = parseFloat(document.getElementById('threshold').value);
                displayLowValues(data, threshold);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function isLowValue(value, threshold) {
            return value <= threshold;
        }

        function displayLowValues(rows, threshold) {
            const dataType = dataTypes[currentDataTypeIndex];
            const filteredRows = rows.filter(row => {
                const value = row[dataType.column] ?? 0;
                return isLowValue(value, threshold);
            });

            // Sort the filtered rows by the selected data type's value in descending order
            filteredRows.sort((a, b) => b[dataType.column] - a[dataType.column]);

            const tableBody = document.querySelector('#generic-data-table tbody');
            tableBody.innerHTML = ''; // Clear previous data

            filteredRows.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.STA_Label}</td>
                    <td>${(row[dataType.column] ?? 0).toFixed(2)}</td>
                `;
                tableBody.appendChild(tr);
            });
        }

        // Initial call to fetch and display data when the page loads
        updateHeaderAndThreshold();
    </script>
</div>
