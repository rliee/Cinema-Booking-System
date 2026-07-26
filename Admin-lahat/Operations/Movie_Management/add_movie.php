<?php
header('Content-Type: application/json');

// Include your MySQLi database connection
require_once __DIR__ . "/../../Includes/connection.php";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
        exit;
    }

    $title      = trim($_POST['title'] ?? '');
    $genreId    = trim($_POST['genre_id'] ?? '');
    $status     = trim($_POST['status'] ?? 'Now Showing');
    $duration   = trim($_POST['duration'] ?? '');
    $rating     = trim($_POST['rating'] ?? 'PG-13');
    $synopsis   = trim($_POST['synopsis'] ?? '');
    $trailerUrl = trim($_POST['trailer_url'] ?? ''); 
    $posterUrl  = ''; 

    if (empty($title) || empty($genreId)) {
        echo json_encode(['success' => false, 'error' => 'Title and genre are required fields.']);
        exit;
    }

    // Handle Poster Image File Upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['poster']['tmp_name'];
        $fileName      = $_FILES['poster']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = 'uploads/';

            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $posterUrl = $dest_path;
            }
        }
    }

    // Insert record including trailer_url using MySQLi Prepared Statements
    $stmt = $conn->prepare("INSERT INTO movies (title, genre_id, movie_status, duration, age_rating, synopsis, poster_url, trailer_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        // Updated type string to "sisissss" to match the 8 parameters
        $stmt->bind_param("sisissss", $title, $genreId, $status, $duration, $rating, $synopsis, $posterUrl, $trailerUrl);
        $success = $stmt->execute();

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database execution failed: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database prepare failed: ' . $conn->error]);
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}