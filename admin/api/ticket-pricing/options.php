<?php

declare(strict_types=1);

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once __DIR__ . "/../../../includes/db.php";
require_once __DIR__ . "/../../../classes/TicketPricingRepository.php";

// Accept GET requests only
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

// Initialize repository
$repository = new TicketPricingRepository($conn);

// Determine requested option type
$type = strtolower(trim($_GET["type"] ?? ""));

switch ($type) {

    case "movies":

        echo json_encode([
            "success" => true,
            "data" => $repository->getAvailableMovies()
        ]);

        break;

    default:

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Invalid option type."
        ]);

        break;
}