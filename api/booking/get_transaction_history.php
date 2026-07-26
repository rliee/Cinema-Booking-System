<?php

header("Content-Type: application/json");


require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../classes/TransactionRepository.php";
require_once __DIR__ . "/../../auth/session.php";



if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


if (!isLoggedIn()) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);

    exit;
}

$user = currentUser();

$userId = $user["id"];



$repository =
    new TransactionRepository($conn);



$transactions =
    $repository->getTransactionHistoryByUserId($userId);



echo json_encode([

    "success" => true,

    "data" => $transactions

]);
