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

// Retrieve a single ticket price (used by the Edit modal)
if (isset($_GET["price_id"])) {

    $priceId = (int) $_GET["price_id"];

    if ($priceId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid ticket price."
        ]);
        exit;
    }

    $ticketPrice = $repository->getTicketPriceById($priceId);

    if (!$ticketPrice) {
        echo json_encode([
            "success" => false,
            "message" => "Ticket price not found."
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "data" => $ticketPrice
    ]);

    exit;
}

// Retrieve a single discount (used by the Edit modal)
if (isset($_GET["discount_id"])) {

    $discountId = (int) $_GET["discount_id"];

    if ($discountId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid discount."
        ]);
        exit;
    }

    $discount = $repository->getDiscountById($discountId);

    if (!$discount) {
        echo json_encode([
            "success" => false,
            "message" => "Discount not found."
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "data" => $discount
    ]);

    exit;
}


echo json_encode([
    "success" => true,
    "data" => [
        "ticketPrices" => $repository->getTicketPrices(),
        "discounts"    => $repository->getDiscounts()
    ]
]);
