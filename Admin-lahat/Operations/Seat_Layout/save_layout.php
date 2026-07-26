<?php
// Prevent PHP notices/warnings from corrupting JSON output
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // 1. Connect directly using connection.php
    require_once __DIR__ . "/../../Includes/connection.php";

    if (!isset($conn) || !$conn) {
        throw new Exception("Database connection failed (\$conn variable not found).");
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['hall_id'])) {
        throw new Exception("Invalid request payload or missing hall_id.");
    }

    $hall_id = intval($input['hall_id']);
    $seats = isset($input['seats']) ? $input['seats'] : [];

    // Fallback if sent via layout object (groupA..D)
    if (empty($seats) && isset($input['layout'])) {
        $groupMap = ['groupA' => 1, 'groupB' => 2, 'groupC' => 3, 'groupD' => 4];
        foreach ($groupMap as $gKey => $gNum) {
            if (isset($input['layout'][$gKey]) && is_array($input['layout'][$gKey])) {
                foreach ($input['layout'][$gKey] as $sIdx => $stVal) {
                    $seats[] = [
                        'group_number' => $gNum,
                        'seat_number'  => intval($sIdx) + 1,
                        'status'       => intval($stVal)
                    ];
                }
            }
        }
    }

    if (empty($seats)) {
        throw new Exception("No seat data provided.");
    }

    $conn->begin_transaction();

    // 2. Update ONLY the seat_status of each existing seat row. Seats are
    //    already seeded (see seat_layout.php), so this never deletes or
    //    re-inserts the whole layout -- it just flips the status column on
    //    the matching (hall_id, seat_row, seat_number) record.
    //
    //    NOTE: we can't rely on mysqli's affected_rows to detect "no seat
    //    found" because affected_rows is also 0 when a row matched but its
    //    status didn't actually change. So existing seats for this hall are
    //    loaded up front, and only seats truly missing from the table fall
    //    back to an INSERT (safety net in case the hall was never seeded).
    $existingSeats = [];
    $existingStmt = $conn->prepare("SELECT seat_row, seat_number FROM seats WHERE hall_id = ?");
    if (!$existingStmt) {
        throw new Exception("Prepare existing-seats lookup failed: " . $conn->error);
    }
    $existingStmt->bind_param("i", $hall_id);
    if (!$existingStmt->execute()) {
        throw new Exception("Existing-seats lookup failed: " . $existingStmt->error);
    }
    $existingResult = $existingStmt->get_result();
    while ($row = $existingResult->fetch_assoc()) {
        $existingSeats[$row['seat_row'] . '-' . $row['seat_number']] = true;
    }
    $existingStmt->close();

    $updateStmt = $conn->prepare("
        UPDATE seats
        SET seat_status = ?
        WHERE hall_id = ? AND seat_row = ? AND seat_number = ?
    ");

    if (!$updateStmt) {
        throw new Exception("Prepare update statement failed: " . $conn->error);
    }

    $insertStmt = $conn->prepare("
        INSERT INTO seats (hall_id, seat_row, seat_number, seat_label, seat_status)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$insertStmt) {
        throw new Exception("Prepare insert statement failed: " . $conn->error);
    }

    foreach ($seats as $s) {
        $gNum   = intval($s['group_number']);
        $sIdx   = intval($s['seat_number']) - 1; // 0..35
        $status = intval(isset($s['seat_status']) ? $s['seat_status'] : $s['status']);

        if ($sIdx < 0 || $sIdx >= 36) continue;

        $rIdx = (int) floor($sIdx / 6);
        $cIdx = $sIdx % 6;

        // Map quadrant group (1..4) back to seat_row (A..L) & seat_number (1..12)
        if ($gNum === 1) {        // Group A (Rows A-F, Seats 1-6)
            $seatRow = chr(65 + $rIdx);
            $seatNumber = 1 + $cIdx;
        } elseif ($gNum === 2) {  // Group B (Rows A-F, Seats 7-12)
            $seatRow = chr(65 + $rIdx);
            $seatNumber = 7 + $cIdx;
        } elseif ($gNum === 3) {  // Group C (Rows G-L, Seats 1-6)
            $seatRow = chr(71 + $rIdx);
            $seatNumber = 1 + $cIdx;
        } elseif ($gNum === 4) {  // Group D (Rows G-L, Seats 7-12)
            $seatRow = chr(71 + $rIdx);
            $seatNumber = 7 + $cIdx;
        } else {
            continue;
        }

        $seatLabel = $seatRow . $seatNumber;
        $seatKey = $seatRow . '-' . $seatNumber;

        if (isset($existingSeats[$seatKey])) {
            // Normal path: seat already exists, just update its status.
            // Bind param types: i, i, s, i -> seat_status, hall_id, seat_row, seat_number
            $updateStmt->bind_param("iisi", $status, $hall_id, $seatRow, $seatNumber);
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update seat {$seatLabel}: " . $updateStmt->error);
            }
        } else {
            // Safety-net path: seat wasn't seeded yet, insert it once.
            // Bind param types: i, s, i, s, i -> hall_id, seat_row, seat_number, seat_label, seat_status
            $insertStmt->bind_param("isisi", $hall_id, $seatRow, $seatNumber, $seatLabel, $status);
            if (!$insertStmt->execute()) {
                throw new Exception("Failed to insert seat {$seatLabel}: " . $insertStmt->error);
            }
        }
    }

    $updateStmt->close();
    $insertStmt->close();

    $conn->commit();

    ob_clean();
    echo json_encode([
        'status'  => 'success',
        'success' => true,
        'message' => 'Seat statuses updated successfully'
    ]);
} catch (Exception $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        @$conn->rollback();
    }
    ob_clean();
    echo json_encode([
        'status'  => 'error',
        'success' => false,
        'message' => $e->getMessage()
    ]);
}