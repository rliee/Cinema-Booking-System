<?php
require_once __DIR__ . "/../../Includes/connection.php";
// Prevent raw PHP HTML error output from corrupting JSON responses
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Enable MySQLi exception throwing so we can catch query errors gracefully
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

ob_start();
header('Content-Type: application/json; charset=utf-8');

/**
 * Helper function to return JSON response
 */
function jsonResponse(bool $success, ?string $error = null): void
{
    ob_clean();
    echo json_encode(['success' => $success, 'error' => $error]);
    exit;
}

try {
    // 1. Verify Database Connection
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception('Database connection failed: ' . ($conn->connect_error ?? 'Connection variable missing'));
    }

    // 2. Validate Movie ID
    $movieIdRaw = $_POST['id'] ?? $_POST['editMovieId'] ?? null;
    if (!$movieIdRaw || empty($movieIdRaw)) {
        throw new Exception('Missing or invalid Movie ID.');
    }

    $id       = intval($movieIdRaw);
    $title    = trim($_POST['title'] ?? $_POST['editMovieTitle'] ?? '');

    // Check both genre_id and genre form fields
    $genreRaw = $_POST['genre_id'] ?? $_POST['genre'] ?? $_POST['editMovieGenre'] ?? 0;
    $genre    = intval($genreRaw);

    $duration = intval($_POST['duration'] ?? $_POST['editMovieDuration'] ?? 0);
    $rating   = trim($_POST['rating'] ?? $_POST['editMovieRating'] ?? '');
    $status   = trim($_POST['status'] ?? $_POST['editMovieStatus'] ?? '');
    $synopsis = trim($_POST['synopsis'] ?? $_POST['editMovieSynopsis'] ?? '');

    // Ensure genre ID is valid before executing the foreign key query
    if ($genre <= 0) {
        throw new Exception('Please select a valid Genre.');
    }

    // 3. Fetch Existing Poster Path
    $poster_path = '';
    $stmtSelect = $conn->prepare("SELECT poster_url FROM movies WHERE movie_id = ?");
    $stmtSelect->bind_param("i", $id);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    if ($row = $result->fetch_assoc()) {
        $poster_path = $row['poster_url'] ?? '';
    }
    $stmtSelect->close();

    // 4. Process Poster File Upload (if provided)
    $fileKey = isset($_FILES['poster']) ? 'poster' : (isset($_FILES['editMoviePoster']) ? 'editMoviePoster' : null);

    if ($fileKey && isset($_FILES[$fileKey]['error']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES[$fileKey]['tmp_name'];
        $fileName      = $_FILES[$fileKey]['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = './uploads/posters/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $destPath    = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $poster_path = 'uploads/posters/' . $newFileName;
            }
        } else {
            throw new Exception('Invalid file format. Allowed formats: JPG, JPEG, PNG, WEBP.');
        }
    }

    // 5. Update Database Record
    // Note: If your column name in 'movies' table is 'status' instead of 'movie_status', change movie_status to status below
    $stmt = $conn->prepare("UPDATE movies SET title = ?, genre_id = ?, duration = ?, age_rating = ?, movie_status = ?, synopsis = ?, poster_url = ? WHERE movie_id = ?");
    $stmt->bind_param("siissssi", $title, $genre, $duration, $rating, $status, $synopsis, $poster_path, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    jsonResponse(true);
} catch (Exception $e) {
    if (isset($conn) && $conn instanceof mysqli && $conn->ping()) {
        $conn->close();
    }
    jsonResponse(false, $e->getMessage());
}