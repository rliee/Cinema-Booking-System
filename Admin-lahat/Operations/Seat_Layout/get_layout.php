<?php

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once __DIR__ . "/../../Includes/connection.php";

try {
    // 1. Get hall_id from query parameter (defaults to 1)
    $hallId = isset($_GET['hall_id']) ? intval($_GET['hall_id']) : 1;

    // 2. Fetch seats for the requested hall
    $sql = "SELECT seat_row, seat_number, seat_label, seat_status 
            FROM seats 
            WHERE hall_id = ? 
            ORDER BY seat_row ASC, seat_number ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }

    $stmt->bind_param("i", $hallId);
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Fetching result set failed: " . $conn->error);
    }

    // Default structure (36 seats per group quadrant)
    $groups = [
        'groupA' => array_fill(0, 36, 1),
        'groupB' => array_fill(0, 36, 1),
        'groupC' => array_fill(0, 36, 1),
        'groupD' => array_fill(0, 36, 1)
    ];

    $seatsArray = [];
    $occupied = 0;
    $unavailable = 0;
    $capacity = 0;

    // 3. Loop through seats and map into groups
    while ($row = $result->fetch_assoc()) {
        $capacity++;
        $seatRow = strtoupper(trim($row['seat_row']));
        $seatNumber = (int) $row['seat_number'];
        $status = (int) $row['seat_status'];
        $seatLabel = isset($row['seat_label']) ? $row['seat_label'] : ($seatRow . $seatNumber);

        if ($status === 0) $occupied++;
        if ($status === 2) $unavailable++;

        $groupNum = 0;
        $index = -1;
        $isMapped = false;

        $rowCode = !empty($seatRow) ? ord($seatRow[0]) : 0;

        // Map into Quadrants & Indexes (6 rows x 6 cols = 36 per group)
        if ($rowCode >= 65 && $rowCode <= 70) { // Rows A - F
            $rowIndex = $rowCode - 65;

            if ($seatNumber >= 1 && $seatNumber <= 6) { // Group A (1)
                $colIndex = $seatNumber - 1;
                $index = ($rowIndex * 6) + $colIndex;
                $groups['groupA'][$index] = $status;
                $groupNum = 1;
                $isMapped = true;
            } else if ($seatNumber >= 7 && $seatNumber <= 12) { // Group B (2)
                $colIndex = $seatNumber - 7;
                $index = ($rowIndex * 6) + $colIndex;
                $groups['groupB'][$index] = $status;
                $groupNum = 2;
                $isMapped = true;
            }
        } else if ($rowCode >= 71 && $rowCode <= 76) { // Rows G - L
            $rowIndex = $rowCode - 71;

            if ($seatNumber >= 1 && $seatNumber <= 6) { // Group C (3)
                $colIndex = $seatNumber - 1;
                $index = ($rowIndex * 6) + $colIndex;
                $groups['groupC'][$index] = $status;
                $groupNum = 3;
                $isMapped = true;
            } else if ($seatNumber >= 7 && $seatNumber <= 12) { // Group D (4)
                $colIndex = $seatNumber - 7;
                $index = ($rowIndex * 6) + $colIndex;
                $groups['groupD'][$index] = $status;
                $groupNum = 4;
                $isMapped = true;
            }
        }

        // Include normalized data and raw DB values for JS rendering
        $seatsArray[] = [
            'group_number'    => $isMapped ? $groupNum : 1,
            'seat_number'     => $isMapped ? ($index + 1) : $seatNumber,
            'status'          => $status,
            'seat_row'        => $seatRow,
            'raw_seat_number' => $seatNumber,
            'seat_label'      => $seatLabel
        ];
    }

    $stmt->close();

    // 4. Return formatted JSON response
    echo json_encode([
        "status"      => "success",
        "success"     => true,
        "hall_id"     => $hallId,
        "capacity"    => $capacity > 0 ? $capacity : 144,
        "occupied"    => $occupied,
        "unavailable" => $unavailable,
        "groups"      => $groups,
        "seats"       => $seatsArray
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

if (isset($conn) && $conn) {
    $conn->close();
}
?>