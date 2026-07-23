<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "cinema_booking";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection Failed : " . $conn->connect_error);
}

$dailySales = $conn->query("SELECT IFNULL(SUM(amount),0) total_sales FROM payments WHERE payment_status='Complete' AND DATE(payment_date)=CURDATE()")->fetch_assoc()['total_sales'];
$weeklySales = $conn->query("SELECT IFNULL(SUM(amount),0) total_sales FROM payments WHERE payment_status='Complete' AND YEARWEEK(payment_date)=YEARWEEK(CURDATE())")->fetch_assoc()['total_sales'];
$monthlySales = $conn->query("SELECT IFNULL(SUM(amount),0) total_sales FROM payments WHERE payment_status='Complete' AND MONTH(payment_date)=MONTH(CURDATE()) AND YEAR(payment_date)=YEAR(CURDATE())")->fetch_assoc()['total_sales'];

$dailySalesQuery = $conn->query("
SELECT
DATE(payment_date) AS sales_date,
SUM(amount) AS total_sales
FROM payments
WHERE payment_status='Complete'
GROUP BY DATE(payment_date)
ORDER BY sales_date DESC
");
$weeklySalesQuery = $conn->query("
SELECT
YEAR(payment_date) AS year,
WEEK(payment_date) AS week,
SUM(amount) AS total_sales
FROM payments
WHERE payment_status='Complete'
GROUP BY YEAR(payment_date), WEEK(payment_date)
ORDER BY year DESC, week DESC
");
$monthlySalesQuery = $conn->query("
SELECT
DATE_FORMAT(payment_date,'%M %Y') AS month,
SUM(amount) AS total_sales
FROM payments
WHERE payment_status='Complete'
GROUP BY YEAR(payment_date), MONTH(payment_date)
ORDER BY YEAR(payment_date), MONTH(payment_date)
");
$movieSalesQuery = $conn->query("
SELECT
m.movie_name,
SUM(p.amount) AS total_sales,
SUM(b.ticket_qty) AS tickets_sold
FROM payments p
INNER JOIN bookings b
ON p.booking_id=b.booking_id
INNER JOIN movies m
ON b.movie_id=m.movie_id
WHERE p.payment_status='Complete'
GROUP BY m.movie_name
ORDER BY total_sales DESC
");
$dailyLabels=[];
$dailyData=[];

while($row=$dailySalesQuery->fetch_assoc()){

    $dailyLabels[]=$row['sales_date'];
    $dailyData[]=$row['total_sales'];

}
$monthLabels=[];
$monthData=[];

while($row=$monthlySalesQuery->fetch_assoc()){

    $monthLabels[]=$row['month'];
    $monthData[]=$row['total_sales'];

}
$movieLabels=[];
$movieData=[];
$movieTicketData=[];

while($row=$movieSalesQuery->fetch_assoc()){

    $movieLabels[]=$row['movie_name'];
    $movieData[]=$row['total_sales'];
    $movieTicketData[]=(int)$row['tickets_sold'];

}

$result = $conn->query(" SELECT
DATE(p.payment_date) AS sales_date,
m.movie_name,
SUM(b.ticket_qty) AS tickets,
SUM(p.amount) AS total_sales
FROM payments p
INNER JOIN bookings b
ON p.booking_id=b.booking_id
INNER JOIN movies m
ON b.movie_id=m.movie_id
WHERE p.payment_status='Complete'
GROUP BY DATE(p.payment_date),m.movie_name
ORDER BY p.payment_date DESC
limit 25
");
?>