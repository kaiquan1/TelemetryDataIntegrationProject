<!DOCTYPE html>
<html>
<head>
<div class="status-chart">
    <h2>Meter Status</h2>
    <script>
        const today = new Date();
        const todayDateString = today.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });

        let onlineDataArray = [];
        let offlineDataArray = [];

        async function getCount() {
            try {
                let onlineCount = 0;
                let offlineCount = 0;

                const response = await fetch('fetch_table_data.php');
                const fetchedData = await response.json(); // Store fetched data in the global variable

                onlineDataArray = [];
                offlineDataArray = [];
                
                fetchedData.forEach(row => {
                    const date = row.LastUpdated;
                    if (!date) {
                        // Skip rows without a LastUpdated date
                        return;
                    }

                    // Split the date string into parts
                    const parts = date.split(' ');

                    // Extract date parts
                    const datePart = parts[0]; // "2022/21/04"
                    const timePart = parts[1] + ' ' + parts[2]; // "04:00:00 AM"

                    // Split the date part into year, day, and month
                    const dateParts = datePart.split('/');
                    const year = dateParts[0];
                    const day = dateParts[1];
                    const month = dateParts[2];

                    // Construct a valid date string in "yyyy-mm-dd" format
                    const validDateString = `${year}-${month}-${day} ${timePart}`;

                    // Create a Date object from the valid date string
                    const dateObj = new Date(validDateString);
                
                    // Format dateObj into a comparable string format
                    const formattedDate = dateObj.toLocaleDateString('en-US', { year: 'numeric', day: '2-digit', month: '2-digit'});
                    
                    // Compare formatted date with today's date
                    if (formattedDate === todayDateString) {
                        onlineDataArray.push(row); // Add to online data array if it matches today
                        onlineCount++; // Increment online count
                    } else {
                        offlineDataArray.push(row); // Add to offline data array if it does not match today
                        offlineCount++; // Increment offline count
                    }
                });
                console.log(offlineDataArray);
                statusChart.data.datasets[0].data = [onlineCount, offlineCount];
                statusChart.update(); // Update the chart
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            getCount();
            setInterval(getCount, 10000); // Refresh data every 10 seconds
        });
    </script>
    <canvas id="statusChart"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Modal HTML -->
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
                    <!-- Dynamic content will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('statusChart').getContext('2d');
        var statusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Online', 'Offline'],
            datasets: [{
                label: 'Meter Status',
                data: [0,0], // placeholder
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
            },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    // Get the clicked element
                    const element = elements[0];
                    // Get the index of the clicked segment
                    const dataIndex = element.index;
                    // Get the label of the clicked segment
                    const label = statusChart.data.labels[dataIndex];
                    console.log(label);
                    console.log(offlineDataArray);
                    // Populate the modal with table data
                    const tableBody = document.getElementById('modalTableBody');
                    tableBody.innerHTML = ''; // Clear previous data
                    
                    // // Select the appropriate data array based on the clicked label
                    // const pieLabel = (label === 'Online') ? onlineDataArray : offlineDataArray;

                    // // Find the data corresponding to the clicked segment
                    // const filteredData = pieLabel.find(item => item.label === label);
                    
                    if (label === 'Online'){
                        modalTitle.textContent = 'Online Details';
                        if (Array.isArray(onlineDataArray) && onlineDataArray.length > 0) {
                            // Clear existing table rows
                            tableBody.innerHTML = '';

                            // Populate the table with the fetched data
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
                            // Handle case where no matching data is found
                            tableBody.innerHTML = '<tr><td colspan="8">No data available</td></tr>';
                        }                    
                    } else if (label === 'Offline'){
                        modalTitle.textContent = 'Offline Details';
                        if (Array.isArray(offlineDataArray) && offlineDataArray.length > 0) {
                            // Clear existing table rows
                            tableBody.innerHTML = '';

                            // Populate the table with the fetched data
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
                            // Handle case where no matching data is found
                            tableBody.innerHTML = '<tr><td colspan="8">No data available</td></tr>';
                        }
                    }

                    // Show the modal
                    var modal = document.getElementById('myModal');                 
                    modal.style.display = 'block'; // Show the modal
                }
            }
        }
    });
    // Get the modal
    var modal = document.getElementById('myModal');

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName('close')[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = 'none';
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
    </script>
</div>
<style>
    /* Modal Styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
</style>
</head>
</html>