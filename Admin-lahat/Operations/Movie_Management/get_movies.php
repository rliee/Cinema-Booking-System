<?php
require_once __DIR__ . "/../../Includes/connection.php";

// Prevent PHP notices/warnings from corrupting JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

ob_start();
header('Content-Type: application/json; charset=utf-8');

/**
 * Helper function to output clean JSON and stop execution
 */
function jsonResponse(bool $success, $dataOrError = null): void
{
    ob_clean();
    if ($success) {
        echo json_encode(['success' => true, 'data' => $dataOrError]);
    } else {
        echo json_encode(['success' => false, 'error' => $dataOrError]);
    }
    exit;
}

// 1. Check Database Connection
if (!isset($conn) || $conn->connect_error) {
    jsonResponse(false, 'Database connection failed: ' . ($conn->connect_error ?? 'Connection variable missing'));
}

// 2. Fetch Movies with Genre Name
$sql = "
    SELECT m.*, g.genre_name 
    FROM movies m 
    LEFT JOIN genres g ON m.genre_id = g.genre_id 
    ORDER BY m.movie_id DESC
";

$result = $conn->query($sql);

if (!$result) {
    jsonResponse(false, 'SQL Query Error: ' . $conn->error);
}

// 3. Structure Movie Data
$movies = [];

while ($row = $result->fetch_assoc()) {
    $movies[] = [
        'id'          => $row['movie_id'] ?? $row['id'] ?? null,
        'title'       => $row['title'] ?? 'Untitled',
        'genre_name'  => $row['genre_name'] ?? $row['genre'] ?? 'General',
        'duration'    => intval($row['duration'] ?? 0),
        'age_rating'  => $row['age_rating'] ?? $row['age-rating'] ?? $row['rating'] ?? 'PG',
        'status'      => $row['status'] ?? 'now-showing',
        'synopsis'    => $row['synopsis'] ?? '',
        'poster_url'  => $row['poster_url'] ?? ''
    ];
}

$conn->close();

// 4. Return Clean JSON
jsonResponse(true, $movies);