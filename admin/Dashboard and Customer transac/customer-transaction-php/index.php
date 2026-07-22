<?php
include("../database/connection.php");

$sql = "

SELECT

booking_transactions.transaction_code,

customers.customer_name,

movies.movie_title,

booking_transactions.total_amount,

booking_transactions.booking_date,

booking_transactions.booking_status,

booking_transactions.seats,

booking_transactions.total_tickets

FROM booking_transactions

INNER JOIN customers
ON booking_transactions.customer_id=customers.customer_id

INNER JOIN movies
ON booking_transactions.movie_id=movies.movie_id

ORDER BY booking_transactions.booking_date DESC

";

$result=$conn->query($sql);

?>