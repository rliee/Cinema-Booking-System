<?php

require_once __DIR__ . "/../../includes/db.php";

header("Content-Type: application/json");


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


$data = json_decode(file_get_contents("php://input"), true);


if (
    !isset($data["booking_reference"]) ||
    !isset($data["payment_reference"])
) {

    echo json_encode([
        "success" => false,
        "message" => "Missing payment information."
    ]);

    exit;
}



$bookingReference = $conn->real_escape_string(
    $data["booking_reference"]
);


$paymentReference = $conn->real_escape_string(
    $data["payment_reference"]
);



$sql = "
    UPDATE payments p

    INNER JOIN bookings b
        ON p.booking_id = b.booking_id

    SET 
        p.reference_number = '$paymentReference',
        p.payment_status = 'pending'

    WHERE 
        b.booking_reference = '$bookingReference'
";


$result = $conn->query($sql);



if ($result && $conn->affected_rows > 0) {

    echo json_encode([
        "success" => true,
        "message" => "Payment submitted."
    ]);
} else {

    echo json_encode([
        "success" => false,
        "message" => "Payment record not found."
    ]);
}
