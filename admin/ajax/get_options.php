<?php

// return JSON response */
header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

// database connection */
require_once "../../includes/db.php";

// required classes
require_once "../../classes/ScheduleRepository.php";

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

// determine which options are requested
$type = strtolower(trim($_GET["type"] ?? ""));

// return the requested option list
switch ($type) {
    // available movies
    case "movies":
        echo json_encode([
            "success" => true,
            "data" => $repository->getMovies()
        ]);
        break;

    // cinema halls
    case "halls":
        echo json_encode([
            "success" => true,
            "data" => $repository->getHalls()
        ]);
        break;

    // unsupported option type
    default:
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid option type."
        ]);
        break;
}