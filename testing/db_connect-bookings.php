<?php
// Database Credentials
$host     = "localhost"; // O IP address ng DB server mo
$user     = "root";      // Default username sa XAMPP
$password = "";          // Default password (blank sa XAMPP)
$dbname   = "cinema_db"; // Pangalan ng database mo

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to UTF-8 para sa special characters
$conn->set_charset("utf8mb4");
?>