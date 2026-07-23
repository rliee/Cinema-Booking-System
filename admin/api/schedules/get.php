<?php

// return JSON response
header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

// database connection
require_once __DIR__ . "/../../../includes/db.php";

// required classes
require_once __DIR__ . "/../../../classes/ScheduleRepository.php";

// accept GET requests only
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

// initialize repository
$repository = new ScheduleRepository($conn);

// retrieve a single schedule (used by the Edit Schedule modal)
if (isset($_GET["schedule_id"])) {
    $scheduleId = (int) $_GET["schedule_id"];
    if ($scheduleId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid schedule."
        ]);
        exit;
    }

    $schedule = $repository->getScheduleById($scheduleId);

    if (!$schedule) {
        echo json_encode([
            "success" => false,
            "message" => "Schedule not found."
        ]);
        exit;
    }
    echo json_encode([
        "success" => true,
        "data" => $schedule
    ]);
    exit;
}

// retrieve all schedules of a specific movie regardless of day
$search = trim($_GET["search"] ?? "");

if ($search !== "") {
    $schedules = $repository->searchSchedules($search);

    echo json_encode([
        "success" => true,
        "data" => $schedules
    ]);
    exit;
}

if (isset($_GET["show_date"])) {
    // retrieve schedules for a selected date (defaults to today's schedules)
    $schedules = $repository->getSchedulesByDate(
        $_GET["show_date"],
        $search
    );
} else {

    // default: current week
    $week = $_GET["show_week"] ?? date("o-W");

    $schedules = $repository->getSchedulesByWeek(
        $week,
        $search
    );
}

// return schedules
echo json_encode([
    "success" => true,
    "data" => $schedules
]);
