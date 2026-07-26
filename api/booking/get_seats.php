<?php

// require_once "../../config/database.php";
// require_once "../../classes/ScheduleRepository.php";

require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../classes/ScheduleRepository.php";

header("Content-Type: application/json");

if (!isset($_GET["schedule_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Schedule ID is required."
    ]);

    exit;
}

$scheduleId = (int) $_GET["schedule_id"];

$repository = new ScheduleRepository($conn);

$seats = $repository->getSeatsBySchedule($scheduleId);

echo json_encode([
    "success" => true,
    "data" => $seats
]);