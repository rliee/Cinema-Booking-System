<?php
// connection for complete button in pending  to databases

include "customer-transaction-php/connection.php";

if(isset($_POST['transaction_code'])){

    $transaction_code = $_POST['transaction_code'];

    $stmt = $conn->prepare("UPDATE booking_transactions
                            SET booking_status='Completed'
                            WHERE transaction_code=?");

    $stmt->bind_param("s", $transaction_code);

    if($stmt->execute()){

        if($stmt->affected_rows > 0){
            echo "success";
        }else{
            echo "No rows updated";
        }

    }else{
        echo $stmt->error;
    }

    $stmt->close();
}else{
    echo "transaction_code not received";
}

$conn->close();
?>