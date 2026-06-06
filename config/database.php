<?php

$servername = "localhost";

$username = "root";

$password = "";

$database = "regiscore";

$conn = new mysqli(

    $servername,
    $username,
    $password,
    $database

);

if($conn->connect_error){

    die("Connection Failed");

}

?>