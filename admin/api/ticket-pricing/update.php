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

$repository = new TicketPricingRepository($conn);

$priceId = (int) ($_POST["price_id"] ?? 0);
$price   = trim($_POST["price"] ?? "");

// Validation
if ($priceId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid ticket price."
    ]);

    exit;
}

if ($price === "" || !is_numeric($price) || (float)$price < 0) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid ticket price."
    ]);

    exit;
}

$result = $repository->updateTicketPrice(
    $priceId,
    (float) $price
);

echo json_encode($result);