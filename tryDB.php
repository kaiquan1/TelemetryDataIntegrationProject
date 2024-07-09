<?php

$connectionOptions = array(
    "Database"=>"ScadaNetDb",
    "UID"=>"EXTERNE",
    "PWD"=>"EXTERNEPCWIN2"
);

$serverName = "192.168.1.25,14330\SQLEXPRESS";

$conn = sqlsrv_connect($serverName,$connectionOptions);

if(!$conn)
{
    die(print_r(sqlsrv_errors(), true));
}
echo "Connected successfully";


sqlsrv_close($conn);


?>
