<?php

header("Content-Type: application/json");


require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../classes/RefundRepository.php";
require_once __DIR__ . "/../../auth/session.php";

$userId = currentUserId();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

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



$data = json_decode(
    file_get_contents("php://input"),
    true
);



$bookingId =
    $data["booking_id"] ?? null;


$refundReason =
    $data["refund_reason"] ?? null;



if (!$bookingId || !$refundReason) {

    echo json_encode([
        "success" => false,
        "message" => "Missing refund information."
    ]);

    exit;
}



$repository =
    new RefundRepository($conn);



$booking =
    $repository->getBookingRefundDetails(
        $bookingId
    );



if (!$booking) {

    echo json_encode([
        "success" => false,
        "message" => "Booking not found."
    ]);

    exit;
}




if (!$repository->canRefund($booking)) {


    echo json_encode([

        "success" => false,

        "message" =>
        "Only paid bookings can be refunded."

    ]);


    exit;
}




$refund =
    $repository->calculateRefund(
        $booking
    );



$updated =
    $repository->refundBooking(
        $bookingId,
        $userId,
        $refundReason
    );



if (!$updated) {


    echo json_encode([

        "success" => false,

        "message" =>
        "Refund failed."

    ]);


    exit;
}




echo json_encode([

    "success" => true,

    "message" =>
    "Refund processed successfully.",

    "reason" =>
    $refundReason,

    "refund" =>
    $refund

]);