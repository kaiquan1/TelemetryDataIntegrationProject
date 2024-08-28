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
                <td id="battery1">${batteryPercentage}%</td>
                <td id="battery2">
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

        // Add search bar functionality
        const searchBar = document.getElementById('searchBar');
        searchBar.addEventListener('input', filterTable);
    });

    function filterTable() {
        const filter = document.getElementById('searchBar').value.toUpperCase();
        const table = document.getElementById('dataTable');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td')[0];
            if (td) {
                const txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
    }
</script>

<div class="search-container">
    <input type="text" id="searchBar" placeholder="Search for site number..">
</div>

<table id="dataTable">
    <thead>
        <tr>
            <th>Site Number</th>
            <th>Metering 1 (m<sup>3</sup>)</th>
            <th>Metering 2 (m<sup>3</sup>)</th>
            <th>Metering 3 (m<sup>3</sup>)</th>
            <th>Flowrate (m<sup>3</sup>/h)</th>
            <th colspan="2">Meter Remaining Battery</th>
            <th>Logger Remaining Battery (Days)</th>
            <th>Last Updated</th>
        </tr>
    </thead>
    <tbody id="data-body">
        <!-- Table rows will be inserted here -->
    </tbody>
</table>