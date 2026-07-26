<?php

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../classes/ScheduleRepository.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$movieId = (int) ($_GET["movie_id"] ?? 0);

if ($movieId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid movie."
    ]);

    exit;
}

$repository = new ScheduleRepository($conn);

$schedules = $repository->getSchedulesByMovieId($movieId);

echo json_encode([
    "success" => true,
    "data" => $schedules
]);