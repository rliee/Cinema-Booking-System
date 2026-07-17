<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "cinemaroyale_db"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
