<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "cinemaroyale_db";


$conn = new mysqli(
    $host,
    $user,
    $password,
    $database
);


if ($conn->connect_error) {

    die("Connection Failed : "
        . $conn->connect_error);
}


/*
|--------------------------------------------------------------------------
| REVENUE CALCULATION QUERY
|--------------------------------------------------------------------------
|
| Revenue is dynamically calculated:
|
| ticket price - discount
|
| Only PAID bookings are counted.
|
*/


$revenueQuery = "

SELECT

IFNULL(
    SUM(
        tp.price -
        (
            tp.price *
            IFNULL(
                d.discount_percentage,
                0
            ) / 100
        )
    ),
0
) AS total


FROM booking_seats bs


INNER JOIN bookings b
    ON bs.booking_id = b.booking_id


INNER JOIN ticket_prices tp
    ON b.schedule_id IN (

        SELECT schedule_id
        FROM show_schedules ss
        WHERE ss.movie_id = tp.movie_id

    )


LEFT JOIN discounts d
    ON bs.discount_id = d.discount_id

    INNER JOIN payments p
    ON p.booking_id = b.booking_id

WHERE
p.payment_status = 'Paid'

";


/*
|--------------------------------------------------------------------------
| TODAY REVENUE
|--------------------------------------------------------------------------
*/


$todayRevenue = $conn->query(

    $revenueQuery . "

    AND DATE(b.booking_date)=CURDATE()

    "

)->fetch_assoc()["total"];



/*
|--------------------------------------------------------------------------
| WEEK REVENUE
|--------------------------------------------------------------------------
*/


$weekRevenue = $conn->query(

    $revenueQuery . "

    AND YEARWEEK(
        b.booking_date
    )
    =
    YEARWEEK(
        CURDATE()
    )

    "

)->fetch_assoc()["total"];




/*
|--------------------------------------------------------------------------
| MONTH REVENUE
|--------------------------------------------------------------------------
*/


$monthRevenue = $conn->query(

    $revenueQuery . "

    AND MONTH(
        b.booking_date
    )
    =
    MONTH(
        CURDATE()
    )

    AND YEAR(
        b.booking_date
    )
    =
    YEAR(
        CURDATE()
    )

    "

)->fetch_assoc()["total"];




/*
|--------------------------------------------------------------------------
| TOTAL REVENUE
|--------------------------------------------------------------------------
*/


$totalRevenue = $conn->query(

    $revenueQuery

)->fetch_assoc()["total"];





/*
|--------------------------------------------------------------------------
| TOTAL TICKETS SOLD
|--------------------------------------------------------------------------
*/


$totalTickets = $conn->query("

SELECT

COUNT(bs.seat_id) AS total


FROM booking_seats bs


INNER JOIN bookings b

ON bs.booking_id = b.booking_id


INNER JOIN payments p

ON p.booking_id = b.booking_id


WHERE

p.payment_status = 'Paid'


")->fetch_assoc()["total"];





/*
|--------------------------------------------------------------------------
| TOTAL TRANSACTIONS
|--------------------------------------------------------------------------
|
| A transaction = booking record
|
*/


$totalTransactions = $conn->query("

SELECT

COUNT(*) AS total


FROM payments


WHERE

payment_status = 'Paid'


")->fetch_assoc()["total"];





/*
|--------------------------------------------------------------------------
| MOVIE SALES DONUT CHART
|--------------------------------------------------------------------------
*/


$movieQuery = $conn->query("

SELECT

m.title,

COUNT(bs.seat_id) AS total


FROM booking_seats bs


INNER JOIN bookings b

ON bs.booking_id = b.booking_id


INNER JOIN show_schedules ss

ON b.schedule_id = ss.schedule_id


INNER JOIN movies m

ON ss.movie_id = m.movie_id


INNER JOIN payments p

ON p.booking_id = b.booking_id



WHERE

p.payment_status = 'Paid'


GROUP BY

m.title


ORDER BY

total DESC


");





$movieLabels = [];
$movieData = [];


while ($row = $movieQuery->fetch_assoc()) {


    $movieLabels[] =
        $row["title"];


    $movieData[] =
        (int)$row["total"];
}




/*
|--------------------------------------------------------------------------
| PAYMENT RECORDS TABLE
|--------------------------------------------------------------------------
*/
$payments = $conn->query("
    SELECT

        p.payment_id,

        b.booking_reference AS transaction_code,

        CONCAT(
            u.first_name,
            ' ',
            u.last_name
        ) AS fullname,

        m.title AS movie_name,

        'Online Payment' AS payment_method,

        (
            tp.price *
            COUNT(bs.seat_id)
        ) AS amount,

        p.payment_status,

        p.payment_date


    FROM payments p


    INNER JOIN bookings b
        ON p.booking_id = b.booking_id


    INNER JOIN users u
        ON b.user_id = u.id


    INNER JOIN show_schedules ss
        ON b.schedule_id = ss.schedule_id


    INNER JOIN movies m
        ON ss.movie_id = m.movie_id


    INNER JOIN booking_seats bs
        ON b.booking_id = bs.booking_id


    INNER JOIN ticket_prices tp
        ON m.movie_id = tp.movie_id


    WHERE
        p.payment_status IN ('Paid','Complete')


    GROUP BY
        b.booking_id


    ORDER BY
        p.payment_id DESC


    LIMIT 25
");




/*
|--------------------------------------------------------------------------
| BAR CHART DATA
|--------------------------------------------------------------------------
*/


$barLabels = [

    "Today",
    "Week",
    "Month",
    "Total"

];


$barData = [

    (float)$todayRevenue,

    (float)$weekRevenue,

    (float)$monthRevenue,

    (float)$totalRevenue

];





/*
|--------------------------------------------------------------------------
| PENDING BOOKINGS
|--------------------------------------------------------------------------
*/


$pendingQuery = "

SELECT


b.booking_id,

b.booking_reference,


m.title AS movie_title,


COUNT(bs.seat_id)
AS ticket_quantity


FROM bookings b


INNER JOIN booking_seats bs

ON b.booking_id = bs.booking_id


INNER JOIN show_schedules ss

ON b.schedule_id = ss.schedule_id


INNER JOIN movies m

ON ss.movie_id = m.movie_id

INNER JOIN payments p


ON p.booking_id = bs.booking_id





WHERE

p.payment_status = 'Pending'


GROUP BY

b.booking_id


ORDER BY

b.booking_id DESC


LIMIT 4


";



$pendingBookings =
    $conn->query($pendingQuery);



$pendingCount = 0;


if ($pendingBookings) {

    $pendingCount =
        $pendingBookings->num_rows;
}
