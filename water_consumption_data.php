<?php
// water_consumption_data.php

include 'db_connection.php';

// Initialize data array
$data = [];

// Check if the request is an AJAX POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Retrieve POST data
    $metering = $_POST['INF_VALUE'] ?? '';
    $siteLabel = $_POST['STA_LABEL'] ?? '';
    $startDate = $_POST['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';

    // Format the start and end dates
    if ($startDate) {
        $startDate .= ' 00:00:00.0000000';
    }
    if ($endDate) {
        $endDate .= ' 23:45:00.0000000';
    }

    // Prepare SQL query
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
            a.Archive_ID ASC
    ";

    // Execute query with parameters
    $params = [$siteLabel, $startDate, $endDate];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        echo json_encode(["error" => print_r(sqlsrv_errors(), true)]);
        exit;
    }

    // Fetch rows and populate the data array
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Determine the metering value based on selection
        $meteringValue = 0;
        switch ($metering) {
            case 'metering1':
                $meteringValue = intval($row['Metering1'] ?? 0);
                break;
            case 'metering2':
                $meteringValue = intval($row['Metering2'] ?? 0);
                break;
            case 'metering3':
                $meteringValue = intval($row['Metering3'] ?? 0);
                break;
            default:
                // Invalid metering selection
                echo json_encode(["error" => "Invalid metering selection."]);
                exit;
        }

        $data[] = [
            "x" => $row['DateValue']->format('Y-m-d H:i:s'),
            "y" => $meteringValue
        ];
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    // Return data as JSON
    echo json_encode($data);
    exit;
}

// Function to fetch site labels
function getSiteLabels($conn) {
    $siteLabels = [];
    $siteQuery = "SELECT DISTINCT STA_Label FROM [ScadaNetDb].[dbo].[View_Stations]";
    $siteStmt = sqlsrv_query($conn, $siteQuery);

    if ($siteStmt === false) {
        die("Error fetching site labels.");
    }

    while ($row = sqlsrv_fetch_array($siteStmt, SQLSRV_FETCH_ASSOC)) {
        $siteLabels[] = $row['STA_Label'];
    }

    sqlsrv_free_stmt($siteStmt);
    return $siteLabels;
}
?>
