<div class="alarm-message">
    <h2>Alarm Message</h2>
    <h4>Low Battery</h4>

<style>
.low-battery-list {
            margin-top: 15 px;
            color: red;
            margin-bottom: 10px;
        }

</style>



    <table id="dataTable">

        <tbody id="tableBody">
            <!-- Table rows will be added here -->
        </tbody>
    </table>

   <div class="low-battery-list">
        <!-- Filtered STA_Labels will be appended here -->
    </div>
<script>
async function fetchData() {
    try {
        const response = await fetch('fetch_table_data.php');
        const data = await response.json();
        displayLowBatteryStations(data);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
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



function displayLowBatteryStations(rows) {
    const lowBatteryRows = rows.filter(row => {
        const batteryPercentage = row.MeterBattery ?? 0;
        return getBatteryClass(batteryPercentage) === 'low-battery';
    });

    const lowBatteryList = document.querySelector('.low-battery-list');
    lowBatteryList.innerHTML = ''; // Clear previous data

    lowBatteryRows.forEach(row => {
        const item = document.createElement('tr');
        item.className = 'low-battery-item';
        item.textContent = `${row.MeterBattert ?? 0}% ${row.STA_Label}`;
        lowBatteryList.appendChild(item);   
    });
}

// Call fetchData to populate the table and display low battery stations
fetchData();

</script>
</div>
