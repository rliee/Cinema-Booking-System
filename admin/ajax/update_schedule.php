<?php

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once __DIR__ . "/../../includes/db.php";
require_once "././classes/ScheduleValidator.php";
require_once "././classes/ScheduleRepository.php";

/* Accept POST requests only */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

// receives the schedule id
$scheduleId = isset($_POST["schedule_id"])
    ? (int) $_POST["schedule_id"]
    : 0;

if ($scheduleId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid schedule."
    ]);
    exit;
}

/* Form data */
$data = [
    "movie_id"      => $_POST["movie_id"] ?? "",
    "hall_id"       => $_POST["hall_id"] ?? "",
    "show_date"     => $_POST["show_date"] ?? "",
    "start_time"    => $_POST["start_time"] ?? ""
];

// alidate inputs
$validator = new ScheduleValidator();
$errors = $validator->validate($data);

if (!empty($errors)) {
    echo json_encode([
        "success" => false,
        "message" => $errors
    ]);
    exit;
}

// update schedule
$repository = new ScheduleRepository($conn);
$response = $repository->update(
    $scheduleId,
    $data
);

echo json_encode($response);