<?php
/**
 * API endpoint to fetch taken/booked seats for a specific movie schedule.
 * 
 * GET parameters:
 *   - cinema: Cinema hall name (e.g. "Cinema Hall 1")
 *   - date:   Screening date (e.g. "2026-07-22" or "Mon, Jul 22")
 *   - time:   Screening time (e.g. "11:00 AM" or "11:00:00")
 *
 * Returns JSON:
 *   { "success": true, "takenSeats": ["A1", "A2", "B5"] }
 *   or
 *   { "success": false, "message": "error description" }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Read parameters
$cinema = isset($_GET['cinema']) ? trim($_GET['cinema']) : '';
$date   = isset($_GET['date'])   ? trim($_GET['date'])   : '';
$time   = isset($_GET['time'])   ? trim($_GET['time'])   : '';

// Validate parameters
if (empty($cinema) || empty($date) || empty($time)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters: cinema, date, time']);
    exit;
}

try {
    // Normalize time format: convert "HH:MM AM/PM" to "HH:MM:SS" for DB matching
    // The database stores time in HH:MM:SS format
    $dbTime = $time;
    // Check if it's in 12-hour format like "11:00 AM"
    if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $time, $matches)) {
        $hour = (int)$matches[1];
        $minute = (int)$matches[2];
        $ampm = strtoupper($matches[3]);
        if ($ampm === 'PM' && $hour < 12) $hour += 12;
        if ($ampm === 'AM' && $hour === 12) $hour = 0;
        $dbTime = sprintf('%02d:%02d:00', $hour, $minute);
    } elseif (strlen($time) === 5) {
        // Already in HH:MM format, append :00
        $dbTime = $time . ':00';
    }
    
    // First, find the hall_id from cinema_halls table
    $hallStmt = $conn->prepare("SELECT hall_id FROM cinema_halls WHERE hall_name = ?");
    if (!$hallStmt) {
        throw new Exception('Failed to prepare hall lookup: ' . $conn->error);
    }
    $hallStmt->bind_param("s", $cinema);
    $hallStmt->execute();
    $hallResult = $hallStmt->get_result();
    
    if ($hallResult->num_rows === 0) {
        // Hall not found in database - return empty taken seats (no bookings yet)
        echo json_encode([
            'success' => true,
            'takenSeats' => [],
            'cinema' => $cinema,
            'date' => $date,
            'time' => $time,
            'note' => 'Hall not found in database'
        ]);
        $hallStmt->close();
        $conn->close();
        exit;
    }
    
    $hallRow = $hallResult->fetch_assoc();
    $hallId = (int)$hallRow['hall_id'];
    $hallStmt->close();
    
    // Try to find a matching schedule in show_schedules
    $scheduleStmt = $conn->prepare("
        SELECT schedule_id 
        FROM show_schedules 
        WHERE hall_id = ? 
          AND show_date = ? 
          AND start_time = ?
        LIMIT 1
    ");
    
    if (!$scheduleStmt) {
        throw new Exception('Failed to prepare schedule lookup: ' . $conn->error);
    }
    
    $scheduleStmt->bind_param("iss", $hallId, $date, $dbTime);
    $scheduleStmt->execute();
    $scheduleResult = $scheduleStmt->get_result();
    
    $takenSeats = [];
    
    if ($scheduleResult->num_rows > 0) {
        $scheduleRow = $scheduleResult->fetch_assoc();
        $scheduleId = (int)$scheduleRow['schedule_id'];
        $scheduleStmt->close();
        
        // Query bookings for this schedule
        $bookingStmt = $conn->prepare("
            SELECT seats 
            FROM bookings 
            WHERE schedule_id = ? 
              AND seats IS NOT NULL 
              AND seats != ''
        ");
        
        if (!$bookingStmt) {
            throw new Exception('Failed to prepare booking lookup: ' . $conn->error);
        }
        
        $bookingStmt->bind_param("i", $scheduleId);
        $bookingStmt->execute();
        $bookingResult = $bookingStmt->get_result();
        
        while ($row = $bookingResult->fetch_assoc()) {
            if (!empty($row['seats'])) {
                $seatArray = explode(',', $row['seats']);
                foreach ($seatArray as $seat) {
                    $seat = trim($seat);
                    if (!empty($seat)) {
                        $takenSeats[] = $seat;
                    }
                }
            }
        }
        
        $bookingStmt->close();
    } else {
        $scheduleStmt->close();
    }
    
    echo json_encode([
        'success' => true,
        'takenSeats' => $takenSeats,
        'cinema' => $cinema,
        'date' => $date,
        'time' => $time
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();

