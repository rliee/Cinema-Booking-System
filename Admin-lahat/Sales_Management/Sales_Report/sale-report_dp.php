<?php


require_once __DIR__ . "/../Includes/connection.php";

$dailySales = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total_sales FROM payments WHERE payment_status='Complete' AND DATE(payment_date)=CURDATE()")->fetch_assoc()['total_sales'];
$weeklySales = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total_sales FROM payments WHERE payment_status='Complete' AND YEARWEEK(payment_date)=YEARWEEK(CURDATE())")->fetch_assoc()['total_sales'];
$monthlySales = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total_sales FROM payments WHERE payment_status='Complete' AND MONTH(payment_date)=MONTH(CURDATE()) AND YEAR(payment_date)=YEAR(CURDATE())")->fetch_assoc()['total_sales'];

$dailySalesQuery = $conn->query("
SELECT
DATE(payment_date) AS sales_date,
SUM(amount_paid) AS total_sales
FROM payments
WHERE payment_status='Complete'
GROUP BY DATE(payment_date)
ORDER BY sales_date DESC
");
$weeklySalesQuery = $conn->query("
SELECT
YEAR(payment_date) AS year,
WEEK(payment_date) AS week,
SUM(amount_paid) AS total_sales
FROM payments
WHERE payment_status='Complete'
GROUP BY YEAR(payment_date), WEEK(payment_date)
ORDER BY year DESC, week DESC
");
$monthlySalesQuery = $conn->query("
SELECT
    DATE(p.payment_date) AS sales_date,
    m.title,
    SUM(b.ticket_qty) AS tickets,
    SUM(p.amount_paid) AS total_sales
FROM payments p
INNER JOIN bookings b ON p.booking_id=b.booking_id
INNER JOIN movies m ON b.movie_id=m.movie_id
WHERE p.payment_status='Complete'
GROUP BY DATE(p.payment_date), m.title 
ORDER BY sales_date DESC LIMIT 25
");
$movieSalesQuery = $conn->query("
SELECT
m.title,
SUM(p.amount_paid) AS total_sales,
SUM(b.ticket_qty) AS tickets_sold
FROM payments p
INNER JOIN bookings b
ON p.booking_id=b.booking_id
INNER JOIN movies m
ON b.movie_id=m.movie_id
WHERE p.payment_status='Complete'
GROUP BY m.title
ORDER BY total_sales DESC
");
$dailyLabels = [];
$dailyData = [];

while ($row = $dailySalesQuery->fetch_assoc()) {

    $dailyLabels[] = $row['sales_date'];
    $dailyData[] = $row['total_sales'];
}
$monthLabels = [];
$monthData = [];

while ($row = $monthlySalesQuery->fetch_assoc()) {

    $monthLabels[] = $row['month'];
    $monthData[] = $row['total_sales'];
}
$movieLabels = [];
$movieData = [];
$movieTicketData = [];

while ($row = $movieSalesQuery->fetch_assoc()) {

    $movieLabels[] = $row['title'];
    $movieData[] = $row['total_sales'];
    $movieTicketData[] = (int)$row['tickets_sold'];
}

$result = $conn->query(" SELECT
DATE(p.payment_date) AS sales_date,
m.title,
SUM(b.ticket_qty) AS tickets,
SUM(p.amount_paid) AS total_sales
FROM payments p
INNER JOIN bookings b
ON p.booking_id=b.booking_id
INNER JOIN movies m
ON b.movie_id=m.movie_id
WHERE p.payment_status='Complete'
GROUP BY DATE(p.payment_date),m.title
ORDER BY sales_date DESC
limit 25
");