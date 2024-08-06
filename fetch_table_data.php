<?php
include 'db_connection.php';

// SQL query to fetch the latest data for each site
$sql = "
SELECT
    s.STA_Label,
    a1.INF_Value AS Metering1,
    a1.INF_Date AS LastUpdated,
    a2.INF_Value AS Metering2,
    a3.INF_Value AS Metering3,
    a17.INF_Value AS Flowrate,
    a49.INF_Value AS MeterBattery,
    a44.INF_Value AS LoggerBattery
FROM
    [ScadaNetDb].[dbo].[View_Stations] s
OUTER APPLY (
    SELECT TOP 1 
        a.INF_Value,
        a.INF_Date
    FROM [ScadaNetDb].[dbo].[View_ArchivedInformations] a
    WHERE a.STA_SiteNumber = s.STA_SiteNumber AND a.INF_NumberInStation = 1
    ORDER BY a.Archive_ID DESC
) a1
OUTER APPLY (
    SELECT TOP 1 
        a.INF_Value
    FROM [ScadaNetDb].[dbo].[View_ArchivedInformations] a
    WHERE a.STA_SiteNumber = s.STA_SiteNumber AND a.INF_NumberInStation = 2
    ORDER BY a.Archive_ID DESC
) a2
OUTER APPLY (
    SELECT TOP 1 
        a.INF_Value
    FROM [ScadaNetDb].[dbo].[View_ArchivedInformations] a
    WHERE a.STA_SiteNumber = s.STA_SiteNumber AND a.INF_NumberInStation = 3
    ORDER BY a.Archive_ID DESC
) a3
OUTER APPLY (
    SELECT TOP 1 
        a.INF_Value
    FROM [ScadaNetDb].[dbo].[View_ArchivedInformations] a
    WHERE a.STA_SiteNumber = s.STA_SiteNumber AND a.INF_NumberInStation = 17
    ORDER BY a.Archive_ID DESC
) a17
OUTER APPLY (
    SELECT TOP 1 
        a.INF_Value
    FROM [ScadaNetDb].[dbo].[View_ArchivedInformations] a
    WHERE a.STA_SiteNumber = s.STA_SiteNumber AND a.INF_NumberInStation = 49
    ORDER BY a.Archive_ID DESC
) a49
OUTER APPLY (
    SELECT TOP 1 
        a.INF_Value
    FROM [ScadaNetDb].[dbo].[View_ArchivedInformations] a
    WHERE a.STA_SiteNumber = s.STA_SiteNumber AND a.INF_NumberInStation = 44
    ORDER BY a.Archive_ID DESC
) a44
";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(json_encode(array("error" => print_r(sqlsrv_errors(), true))));
}

$data = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $row['LastUpdated'] = $row['LastUpdated'] ? $row['LastUpdated']->format('Y/d/m h:i:s A') : '';
    $data[] = $row;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode($data);
?>
