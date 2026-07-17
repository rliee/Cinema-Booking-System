<?php
include("./includes/db.php");


$totalRevenue = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(total_amount) AS total FROM bookings WHERE status='Paid'"
))['total'] ?? 0;


$todayRevenue = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(total_amount) AS total FROM bookings
WHERE DATE(booking_date)=CURDATE()
AND status='Paid'"
))['total'] ?? 0;


$monthRevenue = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(total_amount) AS total FROM bookings
WHERE MONTH(booking_date)=MONTH(CURDATE())
AND YEAR(booking_date)=YEAR(CURDATE())
AND status='Paid'"
))['total'] ?? 0;


$totalTransactions = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM bookings"
))['total'] ?? 0;

$averageSales = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT AVG(total_amount) AS average
FROM bookings
WHERE status='Paid'"
))['average'] ?? 0;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Finance Dashboard</title>
    <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/finance.css">

</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text">
            <i class="fa-solid fa-wallet"></i>
            Finance & Accounting Dashboard
        </h2>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card ">
                    <div class="card-body">
                        <i class="fa-solid fa-sack-dollar icon"></i>

                        <h5 class="mt-3">Today's Revenue</h5>

                        <h3>
                            ₱
                            <?= number_format($todayRevenue, 2); ?>
                        </h3>

                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card ">
                    <div class="card-body">

                        <i class="fa-solid fa-calendar-day icon"></i>

                        <h5 class="mt-3">Revenue this Week</h5>

                        <h3>
                            ₱

                        </h3>

                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card ">
                    <div class="card-body">

                        <i class="fa-solid fa-calendar ico "></i>

                        <h5 class="mt-3">Revenue this Month</h5>

                        <h3>
                            ₱
                            <?= number_format($monthRevenue, 2); ?>
                        </h3>

                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card ">
                    <div class="card-body">
                        <i class="fa-solid fa-sack-dollar icon"></i>

                        <h5 class="mt-3">Total Revenue</h5>

                        <h3>
                            ₱
                            <?= number_format($totalRevenue, 2); ?>
                        </h3>

                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card ">
                    <div class="card-body">
                        <i class="fa-solid fa-sack-dollar icon"></i>

                        <h5 class="mt-3">Total Transactions</h5>

                        <h3>
                            ₱
                            <?= number_format($totalRevenue, 2); ?>
                        </h3>

                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card ">
                    <div class="card-body">
                        <i class="fa-solid fa-sack-dollar icon"></i>

                        <h5 class="mt-3">Total Ticket Sales</h5>

                        <h3>
                            ₱
                            <?= number_format($totalRevenue, 2); ?>
                        </h3>

                    </div>
                </div>
            </div>

        </div>

        <hr>

        <h4 class="mb-3 paid">
            Recent Paid Transactions
        </h4>

        <table class="table-design ">

            <thead>

                <tr>

                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Movie</th>
                    <th>Amount</th>
                    <th>Date</th>

                </tr>

            </thead>

            <tbody>

                <?php

                $result = mysqli_query(
                    $conn,

                    "SELECT booking_id,
                    customer_name,
                    movie_title,
                    total_amount,
                    booking_date
                    FROM bookings
                    WHERE status='Paid'     
                    ORDER BY booking_date DESC
                    LIMIT 10"
                );

                while ($row = mysqli_fetch_assoc($result)) {

                ?>

                    <tr>

                        <td>
                            <?= $row['booking_id']; ?>
                        </td>

                        <td>
                            <?= $row['customer_name']; ?>
                        </td>

                        <td>
                            <?= $row['movie_title']; ?>
                        </td>

                        <td>₱
                            <?= number_format($row['total_amount'], 2); ?>
                        </td>

                        <td>
                            <?= $row['booking_date']; ?>
                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</body>

</html>