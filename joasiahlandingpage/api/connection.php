<?php

$server_name = "localhost";
$database_username = "root";
$database_password = "";
$database_name = "landingpage";

$connection = mysqli_connect(
    $server_name,
    $database_username,
    $database_password,
    $database_name
);

if(!$connection){
    die("Connection Failed: ".mysqli_connect_error());
}

?>