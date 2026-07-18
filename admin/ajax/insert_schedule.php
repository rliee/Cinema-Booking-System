<?php

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once __DIR__ . "/../../includes/db.php";
require_once "././classes/ScheduleValidator.php";
require_once "././classes/ScheduleRepository.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

$data = [
    "movie_id"      => $_POST["movie_id"] ?? "",
    "hall_id"       => $_POST["hall_id"] ?? "",
    "show_date"     => $_POST["show_date"] ?? "",
    "start_time"    => $_POST["start_time"] ?? "",
    "ticket_price"  => $_POST["ticket_price"] ?? ""
];

$validator = new ScheduleValidator();
$errors = $validator->validate($data);

if (!empty($errors)) {
    http_response_code(400);
    
    echo json_encode([
        "success" => false,
        "message" => $errors
    ]);
    exit;
}

$repository = new ScheduleRepository($conn);
$response = $repository->insert($data);

echo json_encode($response);