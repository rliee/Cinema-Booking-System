<?php

session_start();

header("Content-Type: application/json");

$data = json_decode(
    file_get_contents("php://input"),
    true
);


if (!$data) {

    echo json_encode([
        "success" => false,
        "message" => "No booking data received."
    ]);

    exit;
}


$_SESSION["booking"] = $data;


echo json_encode([
    "success" => true,
    "message" => "Booking session saved."
]);
