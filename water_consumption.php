<?php
include 'db_connection.php';

// Initialize data and site labels array
$data = [];
$siteLabels = [];

// Fetch available site labels dynamically from the database
$siteQuery = "SELECT DISTINCT STA_Label FROM [ScadaNetDb].[dbo].[View_Stations]";
$siteStmt = sqlsrv_query($conn, $siteQuery);

if ($siteStmt === false) {
    die(json_encode(array("error" => print_r(sqlsrv_errors(), true))));
}

// Populate the site labels array
while ($row = sqlsrv_fetch_array($siteStmt, SQLSRV_FETCH_ASSOC)) {
    $siteLabels[] = $row['STA_Label'];
}

sqlsrv_free_stmt($siteStmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $metering = $_POST['INF_VALUE'];
    $siteLabel = $_POST['STA_LABEL'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Format the start and end dates to include the full time range as in DB
    if ($startDate) {
        $startDate .= ' 00:00:00.0000000';
    }
    if ($endDate) {
        $endDate .= ' 23:45:00.0000000';
    }

    // Debug inputs
    echo "<script>console.log('Inputs:', " . json_encode(['metering' => $metering, 'siteLabel' => $siteLabel, 'startDate' => $startDate, 'endDate' => $endDate]) . ");</script>";

    // Correct SQL query and ensure it matches the data structure
    $sql = "
    SELECT
        s.STA_Label,
        a.INF_Date AS DateValue, 
        a1.INF_Value AS Metering1,
        a2.INF_Value AS Metering2,
        a3.INF_Value AS Metering3
    FROM
        [ScadaNetDb].[dbo].[View_Stations] s
    INNER JOIN 
        [ScadaNetDb].[dbo].[View_ArchivedInformations] a 
        ON s.STA_SiteNumber = a.STA_SiteNumber
    LEFT JOIN 
        [ScadaNetDb].[dbo].[View_ArchivedInformations] a1 
        ON a.STA_SiteNumber = a1.STA_SiteNumber AND a1.INF_NumberInStation = 1 AND a.INF_Date = a1.INF_Date
    LEFT JOIN 
        [ScadaNetDb].[dbo].[View_ArchivedInformations] a2 
        ON a.STA_SiteNumber = a2.STA_SiteNumber AND a2.INF_NumberInStation = 2 AND a.INF_Date = a2.INF_Date
    LEFT JOIN 
        [ScadaNetDb].[dbo].[View_ArchivedInformations] a3 
        ON a.STA_SiteNumber = a3.STA_SiteNumber AND a3.INF_NumberInStation = 3 AND a.INF_Date = a3.INF_Date
    WHERE 
        s.STA_Label = ? 
        AND a.INF_Date BETWEEN ? AND ?
    ORDER BY 
        a.Archive_ID DESC
    ";

    // Execute query with parameters
    $params = [$siteLabel, $startDate, $endDate];
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Check if SQL query execution failed
    if ($stmt === false) {
        die(json_encode(array("error" => print_r(sqlsrv_errors(), true))));
    }

    // Debug: Check if rows are being fetched correctly
   
    // Fetch rows and populate the data array
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (sqlsrv_has_rows($stmt) === false) {
            echo "<script>console.log('No rows returned');</script>";
        } else {
            echo "<script>console.log('Rows fetched');</script>";
        }
        // Log fetched data row
        echo "<script>console.log('Fetched row:', " . json_encode($row) . ");</script>";
        
        
        // Define mapping for metering columns
        $meteringValue = 0;
        switch ($metering) {
            case 'metering1':
                $meteringValue = $row['Metering1'] ?? 0;
                break;
            case 'metering2':
                $meteringValue = $row['Metering2'] ?? 0;
                break;
            case 'metering3':
                $meteringValue = $row['Metering3'] ?? 0;
                break;
            default:
                die(json_encode(array("error" => "Invalid metering selection.")));
        }
        echo "<script>console.log('Fetched row:', " . json_encode($meteringValue) . ");</script>";
        
            // Add data to the array, ensuring that the correct column is accessed
            $data[] = [
                "x" => $row['DateValue'], // Ensure this matches your actual date field
                "y" => $row[$meteringValue] ?? 0
            ];
   
        
    }
    // Debug: Check if data array is being populated
    echo "<script>console.log('Data array:', " . json_encode($data) . ");</script>";

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>


<!DOCTYPE html>
<html>
<head>
    <style>
        /* Your CSS styles remain the same */
    </style>
</head>
<body>
    <div class="water-consumption">
        <h2>Water Consumption</h2>
        <div class="form1">
            <form name="form1" id="form1" method="post">
                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" name="startDate">
                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" name="endDate">
                <br><br>
                <label for="INF_VALUE">Metering:</label>
                <select name="INF_VALUE" id="INF_VALUE">
                    <option value="metering1">Metering 1</option>
                    <option value="metering2">Metering 2</option>
                    <option value="metering3">Metering 3</option>
                </select>
                <label for="STA_LABEL">Site:</label>
                <select name="STA_LABEL" id="STA_LABEL">
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
        <canvas id="waterChart"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Data passed from PHP to JavaScript
                const chartData = <?php echo json_encode($data); ?>;

                // Extract labels (dates) and values (metering data)
                const labels = chartData.map(item => item.x);
                const dataValues = chartData.map(item => item.y);

                // Get the context of the canvas element
                var ctx = document.getElementById('waterChart').getContext('2d');

                // Create the chart
                var waterConsumptionChart = new Chart(ctx, {
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
                                beginAtZero: true
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
            });
        </script>
    </div>
</body>
</html>
