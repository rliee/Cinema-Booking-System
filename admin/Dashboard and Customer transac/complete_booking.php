<?php
// connection for complete button in pending  to databases

require_once __DIR__ . "/customer-transaction-php/connection.php";



if (isset($_POST["transaction_code"])) {


    $transactionCode = $_POST["transaction_code"];



    $stmt = $conn->prepare("

    UPDATE payments p

    INNER JOIN bookings b
        ON p.booking_id = b.booking_id

    SET 
        p.payment_status = 'Paid'

    WHERE 
        b.booking_reference = ?

");



    $stmt->bind_param(
        "s",
        $transactionCode
    );



    if ($stmt->execute()) {


        if ($stmt->affected_rows > 0) {


            echo "success";
        } else {


            echo "No rows updated";
        }
    } else {


        echo $stmt->error;
    }



    $stmt->close();
} else {


    echo "transaction_code not received";
}



$conn->close();
