<?php
session_start();
require_once __DIR__ . "/../includes/db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $user_id     = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;
    $movie_title = !empty($data['movie']) ? $conn->real_escape_string($data['movie']) : '';
    $cinema_hall = !empty($data['cinema']) ? $conn->real_escape_string($data['cinema']) : '';

    // Format date properly for MySQL (YYYY-MM-DD)
    $raw_date    = !empty($data['date']) ? $data['date'] : '';
    $show_date   = !empty($raw_date) ? date('Y-m-d', strtotime($raw_date)) : date('Y-m-d');

    $show_time   = !empty($data['time']) ? $conn->real_escape_string($data['time']) : '';
    $seats       = !empty($data['seats']) ? $conn->real_escape_string($data['seats']) : '';
    $total_price = !empty($data['total']) ? floatval($data['total']) : 0.00;
    $status      = 'Active';

    if (!empty($movie_title)) {
        // SQL query matching your existing table columns
        $sql = "INSERT INTO bookings (user_id, movie_title, cinema_hall, show_date, show_time, seats, total_price, status) 
                VALUES ($user_id, '$movie_title', '$cinema_hall', '$show_date', '$show_time', '$seats', $total_price, '$status')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true, 'booking_id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing movie title']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
