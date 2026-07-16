<?php

// return JSON response
header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

// database connection
require_once "./includes/db.php";

// required classes
require_once "./classes/ScheduleRepository.php";

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

// retrieve schedules for a selected date (defaults to today's schedules)
$date = $_GET["show_date"] ?? date("Y-m-d");
$schedules = $repository->getSchedulesByDate($date);

// return schedules
echo json_encode([
    "success" => true,
    "data" => $schedules
]);






// include("./includes/db.php");

// $sql = "SELECT ss.schedule_id, m.title, h.hall_name, h.total_seats, ss.ticket_price, TIME_FORMAT(ss.start_time,'%H:%i') start_time, ss.status,
//     IFNULL(SUM(b.seats_booked),0) sold
//     FROM show_schedules ss
//     JOIN movies m
//     ON ss.movie_id=m.movie_id
//     JOIN cinema_halls h
//     ON ss.hall_id=h.hall_id
//     LEFT JOIN bookings b
//     ON ss.schedule_id=b.schedule_id
//     GROUP BY ss.schedule_id
//     ORDER BY ss.show_date,start_time
// ";

// $result = mysqli_query($conn, $sql);

// $data = [];

// while ($row = mysqli_fetch_assoc($result)) {
//     $row["percent"] =
//         ($row["sold"] / $row["total_seats"]) * 100;

//     switch ($row["status"]) {
//         case "Upcoming":
//             $row["class"] = "upcoming";
//             break;
//         case "Now Showing":
//             $row["class"] = "now";
//             break;
//         default:
//             $row["class"] = "completed";
//     }
//     $data[] = $row;
// };
// echo json_encode($data);