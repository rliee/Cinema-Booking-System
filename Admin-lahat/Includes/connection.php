<?php
// Database configuration
$servername = "localhost";
$username = "root"; 
$password = "Saliedo04242620";
$dbname = "cinemaroyale_db";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
?>