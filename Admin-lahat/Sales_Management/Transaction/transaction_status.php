<?php
// update_status.php
require_once __DIR__ . "/../../Includes/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['transaction_code']) && !empty($_POST['status'])) {
    $transaction_code = trim($_POST['transaction_code']);
    $status = trim($_POST['status']);

    $allowed = ['Pending', 'Completed', 'Cancelled'];

    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE booking_transactions SET booking_status = ? WHERE transaction_code = ?");
        $stmt->bind_param("ss", $status, $transaction_code);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error updating record: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Invalid status provided.";
    }
} else {
    echo "Missing required parameters.";
}
?>