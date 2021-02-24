<?php
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $db_name = "banking_system";

    if (!($db = mysqli_connect($hostname, $username, $password, $db_name))) {
        die("Connection failed: ". mysqli_connect_error());
    }
?>