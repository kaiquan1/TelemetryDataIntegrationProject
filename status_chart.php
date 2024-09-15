<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meter Status Chart</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 80%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 800px;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            word-wrap: break-word;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            /* Adjustments for tablets and smaller screens */
            .modal-content {
                width: 95%;
            }

            table th, table td {
                padding: 6px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            /* Adjustments for mobile devices */
            .status-chart {
                margin: 10px;
                padding: 10px;
            }

            table th, table td {
                padding: 4px;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="status-chart">
        <h2>Meter Status</h2>

        <canvas id="statusChart"></canvas>

        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modalTitle"></h2>
                <table id="modalTable">
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
                    <tbody id="modalTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            // Initialize arrays to hold data
            let onlineDataArray = [];
            let offlineDataArray = [];

            // Initialize the chart variable
            let statusChart;

            // Function to fetch and process data
            async function getCount() {
                try {
                    const response = await fetch('fetch_table_data.php');
                    const fetchedData = await response.json();

                    // Reset data arrays
                    onlineDataArray = [];
                    offlineDataArray = [];

                    let onlineCount = 0;
                    let offlineCount = 0;

                    // Get today's date in 'MM/DD/YYYY' format
                    const today = new Date();
                    const todayDateString = today.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });

                    fetchedData.forEach(row => {
                        const date = row.LastUpdated;
                        if (!date) {
                            // Skip rows without a LastUpdated date
                            return;
                        }

                        // Parse the date string into a Date object
                        const dateObj = new Date(date);

                        if (isNaN(dateObj)) {
                            // Invalid date format
                            return;
                        }

                        // Format the date to 'MM/DD/YYYY' for comparison
                        const formattedDate = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });

                        if (formattedDate === todayDateString) {
                            onlineDataArray.push(row);
                            onlineCount++;
                        } else {
                            offlineDataArray.push(row);
                            offlineCount++;
                        }
                    });

                    // Update the chart data
                    if (statusChart) {
                        statusChart.data.datasets[0].data = [onlineCount, offlineCount];
                        statusChart.update();
                    } else {
                        initializeChart([onlineCount, offlineCount]);
                    }

                } catch (error) {
                    console.error('Error fetching data:', error);
                }
            }

            // Function to initialize the Chart.js pie chart
            function initializeChart(data) {
                const ctx = document.getElementById('statusChart').getContext('2d');
                statusChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Online', 'Offline'],
                        datasets: [{
                            label: 'Meter Status',
                            data: data, // [onlineCount, offlineCount]
                            backgroundColor: ['#36a2eb', '#ff6384'],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true, // Enable responsiveness
                        maintainAspectRatio: false, // Allow height to be set via CSS
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
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const element = elements[0];
                                const dataIndex = element.index;
                                const label = statusChart.data.labels[dataIndex];

                                populateModal(label);
                            }
                        }
                    }
                });
            }

            // Function to populate and show the modal
            function populateModal(label) {
                const modal = document.getElementById('myModal');
                const modalTitle = document.getElementById('modalTitle');
                const tableBody = document.getElementById('modalTableBody');

                tableBody.innerHTML = ''; // Clear previous data

                if (label === 'Online') {
                    modalTitle.textContent = 'Online Details';
                    if (Array.isArray(onlineDataArray) && onlineDataArray.length > 0) {
                        onlineDataArray.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.STA_Label || 'N/A'}</td>
                                <td>${item.Metering1 || 'N/A'}</td>
                                <td>${item.Metering2 || 'N/A'}</td>
                                <td>${item.Metering3 || 'N/A'}</td>
                                <td>${item.Flowrate || 'N/A'}</td>
                                <td>${item.MeterBattery || 'N/A'}</td>
                                <td>${item.LoggerBattery || 'N/A'}</td>
                                <td>${item.LastUpdated || 'N/A'}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="8">No data available</td></tr>';
                    }                    
                } else if (label === 'Offline') {
                    modalTitle.textContent = 'Offline Details';
                    if (Array.isArray(offlineDataArray) && offlineDataArray.length > 0) {
                        offlineDataArray.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.STA_Label || 'N/A'}</td>
                                <td>${item.Metering1 || 'N/A'}</td>
                                <td>${item.Metering2 || 'N/A'}</td>
                                <td>${item.Metering3 || 'N/A'}</td>
                                <td>${item.Flowrate || 'N/A'}</td>
                                <td>${item.MeterBattery || 'N/A'}</td>
                                <td>${item.LoggerBattery || 'N/A'}</td>
                                <td>${item.LastUpdated || 'N/A'}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="8">No data available</td></tr>';
                    }
                }

                // Show the modal
                modal.style.display = 'block';
            }

            // Function to close the modal
            function closeModal() {
                const modal = document.getElementById('myModal');
                modal.style.display = 'none';
            }

            // Event listeners for modal close actions
            document.addEventListener('DOMContentLoaded', () => {
                // Initial data fetch
                getCount();
                // Refresh data every 10 seconds
                setInterval(getCount, 10000);

                // Close modal when clicking on <span> (x)
                const span = document.getElementsByClassName('close')[0];
                span.onclick = closeModal;

                // Close modal when clicking outside of the modal content
                window.onclick = function(event) {
                    const modal = document.getElementById('myModal');
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                }
            });
        </script>
    </div>
</body>
</html>
