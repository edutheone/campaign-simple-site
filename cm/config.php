<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$host = "localhost";
$username = "root";        
$password = "";            
$database = "campaign_db"; 


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}


$conn->set_charset("utf8mb4");
?>