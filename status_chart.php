<!DOCTYPE html>
<html>
<head>
<div class="status-chart">
    <h2>Meter Status</h2>
    <script>
        let onlineCount = 0;
        let offlineCount = 0;

        const today = new Date();
        const todayDateString = today.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });

        async function getData() {
            try {
                onlineCount = 0;
                offlineCount = 0;
                formattedDatesArray = [];
                const response = await fetch('fetch_table_data.php');
                const fetchedData = await response.json(); // Store fetched data in the global variable
                
                // Extract last updated dates from fetched data
                const lastUpdatedDates = fetchedData.map(row => row.LastUpdated).filter(date => date); // Filter out null or undefined dates               

                const todayDates = lastUpdatedDates.filter(date => {
                // Parse date from data into a Date object
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
                    
                    formattedDatesArray.push(formattedDate);
                });

                formattedDatesArray.forEach(date => {
                    if(date === todayDateString)
                    {
                        onlineCount++; // Increment online count if dates match
                    } else {
                        offlineCount++; // Decrement offline count if dates do not match
                    }              
                });
                statusChart.data.datasets[0].data = [onlineCount, offlineCount];
                statusChart.update(); // Update the chart
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            getData();
            setInterval(getData, 10000); // Refresh data every 10 seconds
        });
    </script>
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
            }
        }
    });
    </script>
</div>
</head>
</html>