<?php
// 1. Turn off inline HTML display of PHP errors/warnings
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Start output buffer immediately to capture any accidental output or whitespaces
ob_start();

header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => ''];

try {
    require_once __DIR__ . "/../../Includes/connection.php";

    // Discard any extra output created during connection/file loading
    ob_clean();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $hallId   = isset($_POST['hall_id']) ? intval($_POST['hall_id']) : 0;
    $statusId = isset($_POST['status_id']) ? intval($_POST['status_id']) : (isset($_POST['status']) ? intval($_POST['status']) : 0);

    if ($hallId <= 0 || $statusId <= 0) {
        throw new Exception("Invalid parameters. Received Hall ID: {$hallId}, Status ID: {$statusId}");
    }

    // Query has 2 placeholders
    $stmt = $conn->prepare("UPDATE cinema_halls SET status_id = ? WHERE hall_id = ?");

    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    // FIXED: Matched 2 placeholders ("ii") with 2 variables ($statusId, $hallId)
    $stmt->bind_param("ii", $statusId, $hallId);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $statusName = 'OPERATIONAL';
    $badgeClass = 'operational';
    $seatsWereReset = false;

    if ($statusId === 2) {
        $statusName = 'UNDER MAINTENANCE';
        $badgeClass = 'maintenance under-maintenance';
    } elseif ($statusId === 3) {
        $statusName = 'CLOSED';
        $badgeClass = 'closed';
    }

    // When a hall goes under maintenance or closed, nobody can be seated in it,
    // so automatically clear every seat back to Available (occupied/unavailable -> 0).
    if ($statusId === 2 || $statusId === 3) {
        $resetStmt = $conn->prepare("UPDATE seats SET seat_status = 1 WHERE hall_id = ?");
        if ($resetStmt) {
            $resetStmt->bind_param("i", $hallId);
            if ($resetStmt->execute()) {
                $seatsWereReset = true;
            }
            $resetStmt->close();
        }
    }

    $response = [
        'success'         => true,
        'hall_id'         => $hallId,
        'status_id'       => $statusId,
        'status_name'     => $statusName,
        'badge_class'     => $badgeClass,
        'seats_reset'     => $seatsWereReset,
        'editing_locked'  => ($statusId === 2 || $statusId === 3)
    ];

    $stmt->close();
} catch (Exception $e) {
    // Catch any error and pass it cleanly into JSON
    ob_clean();
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Guarantee ONLY JSON is outputted
echo json_encode($response);
exit;