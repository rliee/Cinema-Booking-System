<?php

declare(strict_types=1);

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once __DIR__ . "/../../../includes/db.php";
require_once __DIR__ . "/../../../classes/TicketPricingRepository.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$discountId = (int) ($_POST["discount_id"] ?? 0);

if ($discountId <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid discount."
    ]);

    exit;
}

$repository = new TicketPricingRepository($conn);

echo json_encode(
    $repository->deleteDiscount($discountId)
);