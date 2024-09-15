<?php

$serverName = "192.168.1.25,14330\SQLEXPRESS";
$connectionOptions = array(
    "Database"=>"ScadaNetDb",
    "UID"=>"TEMPUSER2",
    "PWD"=>"Cosmos32"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>