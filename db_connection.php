<?php

$serverName = "192.168.1.25,14330\SQLEXPRESS";
$connectionOptions = array(
    "Database"=>"ScadaNetDb",
    "UID"=>"EXTERNE",
    "PWD"=>"EXTERNEPCWIN2"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>