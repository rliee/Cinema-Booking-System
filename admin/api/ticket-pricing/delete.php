<?php

declare(strict_types=1);

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once __DIR__ . "/../../../includes/db.php";
require_once __DIR__ . "/../../../classes/TicketPricingRepository.php";

// Accept POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$priceId = (int) ($_POST["price_id"] ?? 0);

if ($priceId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid ticket price."
    ]);

    exit;
}

$repository = new TicketPricingRepository($conn);

$result = $repository->deleteTicketPrice($priceId);

echo json_encode($result);