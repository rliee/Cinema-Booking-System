<?php
// Suppress warnings to prevent breaking JSON response output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

require_once __DIR__ . "/../../Includes/connection.php";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
        exit;
    }

    $movieId = $_POST['id'] ?? null;

    if (empty($movieId)) {
        echo json_encode(['success' => false, 'error' => 'No movie ID specified.']);
        exit;
    }

    // Optional: Delete physical poster file from server to avoid clutter
    $posterQuery = $conn->prepare("SELECT poster_url FROM movies WHERE movie_id = ?");
    if ($posterQuery) {
        $posterQuery->bind_param("i", $movieId);
        $posterQuery->execute();
        $result = $posterQuery->get_result();
        if ($row = $result->fetch_assoc()) {
            $posterPath = $row['poster_url'];
            if (!empty($posterPath) && file_exists($posterPath) && $posterPath !== 'image.png') {
                @unlink($posterPath);
            }
        }
        $posterQuery->close();
    }

    // Permanent Deletion from Database
    $stmt = $conn->prepare("DELETE FROM movies WHERE movie_id = ?");

    if ($stmt) {
        $stmt->bind_param("i", $movieId);
        $executed = $stmt->execute();

        if ($executed && $stmt->affected_rows > 0) {
            // Added success message in the response array
            echo json_encode(['success' => true, 'message' => 'Movie deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Movie not found or already deleted from database.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database prepare failed: ' . $conn->error]);
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}