<?php

header("Content-Type: application/json");


require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../classes/BookingRepository.php";
require_once __DIR__ . "/../../classes/PaymentRepository.php";
require_once __DIR__ . "/../../auth/session.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}



$data = json_decode(
    file_get_contents("php://input"),
    true
);


if (!isLoggedIn()) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);

    exit;
}

$user = currentUser();

$userId = $user["id"];
$scheduleId = $data["schedule_id"] ?? null;
$seatIds = $data["seat_ids"] ?? [];



if (
    !$userId ||
    !$scheduleId ||
    empty($seatIds)
) {

    echo json_encode([
        "success" => false,
        "message" => "Missing booking information."
    ]);

    exit;
}



$repository = new BookingRepository($conn);


$bookingId = $repository->createBooking(
    $userId,
    $scheduleId,
    $seatIds
);



if ($bookingId) {


    $paymentRepository = new PaymentRepository($conn);

    $payment = $paymentRepository->getPaymentByReference(
        $bookingId
    );

    $paymentCreated = $paymentRepository->createPayment(
        $payment["booking_id"]
    );


    if (!$paymentCreated) {

        echo json_encode([
            "success" => false,
            "message" => "Booking created but payment failed."
        ]);

        exit;
    }
}

if (!$bookingId) {

    echo json_encode([
        "success" => false,
        "message" => "Booking failed."
    ]);

    exit;
}



echo json_encode([

    "success" => true,

    "message" => "Booking created successfully.",

    "booking_reference" => $bookingId

]);
