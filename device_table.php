<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Device Table with Status Indicators</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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

    <script>
        /**
         * Fetches data from the server and updates the table.
         */
        async function fetchData() {
            try {
                const response = await fetch('fetch_table_data.php');
                const data = await response.json();
                updateTable(data);
                updateChartData(data); // If you have a chart, update it here
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        /**
         * Updates the table with fetched data.
         * @param {Array} data - Array of data objects fetched from the server.
         */
        function updateTable(data) {
            const tableBody = document.getElementById('data-body');
            tableBody.innerHTML = ''; // Clear existing data

            data.forEach(row => {
                const tr = document.createElement('tr');

                // Determine if the site is online or offline
                const isOnline = checkIfOnline(row.LastUpdated);
                const statusClass = isOnline ? 'online' : 'offline';
                const statusDot = `<span class="status-dot ${statusClass}" aria-label="${isOnline ? 'Online' : 'Offline'}"></span>`;

                const batteryPercentage = row.MeterBattery ?? 0;

                tr.innerHTML = `
                    <td class="site-number">${statusDot}${row.STA_Label ?? ''}</td>
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
                    <td>${formatDate(row.LastUpdated) ?? ''}</td>
                `;

                tableBody.appendChild(tr);
            });
        }

        /**
         * Checks if the provided date is today's date.
         * @param {string} dateString - The date string to check.
         * @returns {boolean} - True if the date is today, false otherwise.
         */
        function checkIfOnline(dateString) {
            if (!dateString) return false;

            const lastUpdatedDate = new Date(dateString);
            if (isNaN(lastUpdatedDate)) return false;

            const today = new Date();
            return (
                lastUpdatedDate.getDate() === today.getDate() &&
                lastUpdatedDate.getMonth() === today.getMonth() &&
                lastUpdatedDate.getFullYear() === today.getFullYear()
            );
        }

        /**
         * Formats a date string into 'MM/DD/YYYY' format.
         * @param {string} dateString - The date string to format.
         * @returns {string} - Formatted date string.
         */
        function formatDate(dateString) {
            if (!dateString) return '';

            const dateObj = new Date(dateString);
            if (isNaN(dateObj)) return '';

            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            const year = dateObj.getFullYear();

            return `${month}/${day}/${year}`;
        }

        /**
         * Determines the battery class based on the percentage.
         * @param {number} batteryPercentage - The battery percentage.
         * @returns {string} - The battery class name.
         */
        function getBatteryClass(batteryPercentage) {
            if (batteryPercentage <= 20) {
                return 'low-battery';
            } else if (batteryPercentage <= 50) {
                return 'medium-battery';
            } else {
                return 'high-battery';
            }
        }

        /**
         * Initializes event listeners after the DOM is fully loaded.
         */
        document.addEventListener('DOMContentLoaded', () => {
            fetchData(); // Initial data fetch

            // Add search bar functionality
            const searchBar = document.getElementById('searchBar');
            searchBar.addEventListener('input', filterTable);

            // Optionally, set up periodic data fetching
            // setInterval(fetchData, 10000); // Fetch data every 10 seconds
        });

        /**
         * Filters the table rows based on the search input.
         */
        function filterTable() {
            const filter = document.getElementById('searchBar').value.toUpperCase();
            const table = document.getElementById('dataTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
                const td = tr[i].getElementsByTagName('td')[0]; // Site Number column
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    // Remove the status dot symbols from the text
                    const siteNumber = txtValue.replace(/[\u25CF\u25CB]/g, '').trim(); // Removes ● and ○ if present
                    if (siteNumber.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>
