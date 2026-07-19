<?php

// return JSON response
header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

// database connection
require_once __DIR__ . "/../../includes/db.php";

// required classes
require_once __DIR__ . "/../../classes/ScheduleRepository.php";

// accept POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

// retrieve schedule ID
$scheduleId = isset($_POST["schedule_id"])
    ? (int) $_POST["schedule_id"]
    : 0;

// validate schedule ID
if ($scheduleId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid schedule."
    ]);
    exit;
}

// delete schedule
$repository = new ScheduleRepository($conn);
$response = $repository->delete($scheduleId);

// return response
echo json_encode($response);