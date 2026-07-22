<?php
$conn = new mysqli("localhost", "root", "", "cinema_booking");

if (isset($_POST['booking_id'])) {

    $booking_id = $_POST['booking_id'];

    // Kunin ang total amount ng booking
    $booking = $conn->query("
        SELECT total_amount
        FROM bookings
        WHERE booking_id='$booking_id'
    ")->fetch_assoc();

    // Update booking status
    $conn->query("
        UPDATE bookings
        SET booking_status='Confirmed'
        WHERE booking_id='$booking_id'
    ");

    // Gumawa ng transaction code
    $transaction_code = "TXN" . date("YmdHis");

    // Insert sa payments table
    // Random payment method
    $methods = ['GCash', 'PayMaya', 'Paypal'];
    $payment_method = $methods[array_rand($methods)];

    // Random payment status
    $statuses = ['Pending', 'Complete'];
    $payment_status = $statuses[array_rand($statuses)];


    // Insert payment record
    $conn->query("
    INSERT INTO payments
    (
        booking_id,
        transaction_code,
        payment_method,
        amount,
        payment_status
    )
    VALUES
    (
        '$booking_id',
        '$transaction_code',
        '$payment_method',
        '{$booking['total_amount']}',
        '$payment_status'
    )
");

    header("Location: finance.php");
    exit();
}
