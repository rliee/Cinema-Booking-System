<?php
// Include your MySQLi database connection
require_once __DIR__ . "/../../Includes/connection.php";

header('Content-Type: text/html; charset=utf-8');

// Generate 36 seats set to 1 (1 = Available) for all groups in all halls
$allAvailableGroup = array_fill(0, 36, 1);

$hallLayoutData = [
    "hall-1" => [
        "groupA" => $allAvailableGroup,
        "groupB" => $allAvailableGroup,
        "groupC" => $allAvailableGroup,
        "groupD" => $allAvailableGroup
    ],
    "hall-2" => [
        "groupA" => $allAvailableGroup,
        "groupB" => $allAvailableGroup,
        "groupC" => $allAvailableGroup,
        "groupD" => $allAvailableGroup
    ],
    "hall-3" => [
        "groupA" => $allAvailableGroup,
        "groupB" => $allAvailableGroup,
        "groupC" => $allAvailableGroup,
        "groupD" => $allAvailableGroup
    ],
    "hall-4" => [
        "groupA" => $allAvailableGroup,
        "groupB" => $allAvailableGroup,
        "groupC" => $allAvailableGroup,
        "groupD" => $allAvailableGroup
    ]
];

// 1. Clear existing seats safely (DELETE instead of TRUNCATE so this works
//    without DROP privilege, and also removes any duplicate rows left over
//    from the earlier save bug)
$conn->query("SET FOREIGN_KEY_CHECKS = 0;");
$conn->query("DELETE FROM seats;");
$conn->query("ALTER TABLE seats AUTO_INCREMENT = 1;");
$conn->query("SET FOREIGN_KEY_CHECKS = 1;");

$stmt = $conn->prepare("INSERT INTO seats (hall_id, seat_row, seat_number, seat_label, seat_status) VALUES (?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Database prepare failed: " . $conn->error);
}

// Variables bound to statement
$hallId = 0;
$seatRow = '';
$seatNumber = 0;
$seatLabel = '';
$statusVal = 1;

$stmt->bind_param("isisi", $hallId, $seatRow, $seatNumber, $seatLabel, $statusVal);

$insertedCount = 0;

// 2. Start Transaction for high-speed batch insertion
$conn->begin_transaction();

try {
    foreach ($hallLayoutData as $hallKey => $groups) {
        $hallId = (int) str_replace('hall-', '', $hallKey);

        foreach ($groups as $groupKey => $seats) {
            foreach ($seats as $index => $statusVal) {
                $rowIndex = (int) floor($index / 6);
                $colIndex = $index % 6;
                $key = strtoupper((string) $groupKey);

                // Default Fallback (Group A)
                $rowCode = 65 + $rowIndex;  // Rows A - F
                $seatNumber = 1 + $colIndex;   // Seats 1 - 6

                if (strpos($key, 'B') !== false) {
                    $rowCode = 65 + $rowIndex;  // Rows A - F
                    $seatNumber = 7 + $colIndex;   // Seats 7 - 12
                } else if (strpos($key, 'C') !== false) {
                    $rowCode = 71 + $rowIndex;  // Rows G - L
                    $seatNumber = 1 + $colIndex;   // Seats 1 - 6
                } else if (strpos($key, 'D') !== false) {
                    $rowCode = 71 + $rowIndex;  // Rows G - L
                    $seatNumber = 7 + $colIndex;   // Seats 7 - 12
                }

                $seatRow = chr($rowCode);
                $seatLabel = $seatRow . $seatNumber;

                if ($stmt->execute()) {
                    $insertedCount++;
                } else {
                    throw new Exception("Insert failed for seat {$seatLabel} in Hall {$hallId}: " . $stmt->error);
                }
            }
        }
    }

    // Commit all queries at once
    $conn->commit();
    echo "<h3>Success!</h3><p>Populated database with <strong>$insertedCount</strong> seats, all set to Available (1).</p>";

} catch (Exception $e) {
    // Rollback changes if any single query fails
    $conn->rollback();
    echo "<h3>Seeding Failed!</h3><p>Error: " . $e->getMessage() . "</p>";
}

$stmt->close();
$conn->close();
?>