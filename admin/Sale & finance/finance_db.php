<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "cinema_booking";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection Failed : " . $conn->connect_error);
}

// Today's Revenue
$todayRevenue = $conn->query(" SELECT IFNULL(SUM(amount),0) total FROM payments WHERE payment_status='Complete' AND DATE(payment_date)=CURDATE()
")->fetch_assoc()['total'];
// Week Revenue
$weekRevenue = $conn->query(" SELECT IFNULL(SUM(amount),0) total FROM payments WHERE payment_status='Complete' AND YEARWEEK(payment_date)=YEARWEEK(CURDATE())
")->fetch_assoc()['total'];
// Month Revenue
$monthRevenue = $conn->query("SELECT IFNULL(SUM(amount),0) total FROM payments WHERE payment_status='Complete' AND MONTH(payment_date)=MONTH(CURDATE()) AND YEAR(payment_date)=YEAR(CURDATE())
")->fetch_assoc()['total'];
// Total Revenue
$totalRevenue = $conn->query(" SELECT IFNULL(SUM(amount),0) total FROM payments WHERE payment_status='Complete'
")->fetch_assoc()['total'];
// Ticket Sales
$totalTickets = $conn->query(" SELECT IFNULL(SUM(ticket_qty),0) total FROM bookings
")->fetch_assoc()['total'];
// Transactions
$totalTransactions = $conn->query(" SELECT COUNT(*) total FROM payments
")->fetch_assoc()['total'];
// DONUT CHART VALUES
$movieQuery = $conn->query(" SELECT m.movie_name, SUM(b.ticket_qty) AS total FROM bookings b INNER JOIN movies m ON b.movie_id = m.movie_id GROUP BY m.movie_name
");
// PAYMENT RECORDS
$payments = $conn->query(" SELECT 
        p.payment_id,
        p.booking_id,
        p.transaction_code,
        p.payment_method,
        p.amount,
        p.payment_status,
        p.payment_date,
        c.fullname,
        m.movie_name
    FROM payments p
    INNER JOIN bookings b
        ON p.booking_id = b.booking_id
    INNER JOIN customers c
        ON b.customer_id = c.customer_id
    INNER JOIN movies m
        ON b.movie_id = m.movie_id
    ORDER BY p.payment_date DESC
    limit 25

");
//BAR CHART VALUES
$barLabels = ['Today', 'Week', 'Month', 'Total'];
$barData = [
    (float)$todayRevenue,
    (float)$weekRevenue,
    (float)$monthRevenue,
    (float)$totalRevenue
];


$movieLabels = [];
$movieData = [];

while ($row = $movieQuery->fetch_assoc()) {
    $movieLabels[] = $row['movie_name'];
    $movieData[] = (int)$row['total'];
}



// Count pending bookings
$pendingCount = 0;

$pendingQuery = " SELECT 
                        b.booking_id,
                        c.fullname,
                        m.movie_name,
                        b.ticket_qty,
                        b.total_amount
                    FROM bookings b
                    INNER JOIN customers c
                        ON b.customer_id = c.customer_id
                    INNER JOIN movies m
                        ON b.movie_id = m.movie_id
                    WHERE b.booking_status = 'Pending'
                    ORDER BY b.booking_id DESC
                    LIMIT 4
                    ";
$pendingBookings = $conn->query($pendingQuery);

if ($pendingBookings) {
    $pendingCount = $pendingBookings->num_rows;
}

