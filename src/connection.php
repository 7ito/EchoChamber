<?php

    $host = "cosc360.ok.ubc.ca";
    $username = "64216567";
    $password = "64216567";
    $dbname = "db_64216567";

    // $conn = mysqli_connect($host, $username, $password, $dbname);
    $conn = mysqli_connect("localhost", "root", "", "echochamber") or die("Unable to establish connection");
    
?>