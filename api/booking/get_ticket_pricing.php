<?php

require_once __DIR__ . "/../../includes/db.php";

header("Content-Type: application/json");


$movieId = $_GET["movie_id"] ?? null;


if (!$movieId) {

    echo json_encode([
        "success" => false,
        "message" => "Movie ID required"
    ]);

    exit;
}



// Get ticket price
$query = "
    SELECT price
    FROM ticket_prices
    WHERE movie_id = ?
";


$stmt = $conn->prepare($query);

$stmt->bind_param(
    "i",
    $movieId
);

$stmt->execute();


$result = $stmt->get_result();


$priceRow = $result->fetch_assoc();



if (!$priceRow) {

    echo json_encode([
        "success" => false,
        "message" => "Ticket price not found"
    ]);

    exit;
}


// Get discounts
$discountQuery = "
    SELECT discount_name, discount_percentage
    FROM discounts
";


$discountResult = $conn->query($discountQuery);


$discounts = [];

while ($row = $discountResult->fetch_assoc()) {

    $name = strtolower($row["discount_name"]);

    // Normalize database names to match frontend keys
    if ($name === "senior citizen") {
        $name = "senior";
    }

    $discounts[$name] = $row["discount_percentage"];
}

echo json_encode([
    "success" => true,
    "price" => $priceRow["price"],
    "discounts" => $discounts
]);