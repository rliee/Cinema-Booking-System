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
| SALES CALCULATION BASE QUERY
|--------------------------------------------------------------------------
|
| Revenue is dynamically calculated:
|
| ticket price - discount
|
| Only PAID payments are counted.
|
*/


$salesQuery = "

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
) AS total_sales


FROM booking_seats bs


INNER JOIN bookings b
    ON bs.booking_id = b.booking_id


INNER JOIN payments p
    ON b.booking_id = p.booking_id


INNER JOIN show_schedules ss
    ON b.schedule_id = ss.schedule_id


INNER JOIN movies m
    ON ss.movie_id = m.movie_id


INNER JOIN ticket_prices tp
    ON m.movie_id = tp.movie_id


LEFT JOIN discounts d
    ON bs.discount_id = d.discount_id


WHERE

p.payment_status = 'Paid'

";





/*
|--------------------------------------------------------------------------
| DAILY SALES
|--------------------------------------------------------------------------
*/


$dailySales = $conn->query(

    $salesQuery . "

    AND DATE(
        p.payment_date
    ) = CURDATE()

    "

)->fetch_assoc()["total_sales"];





/*
|--------------------------------------------------------------------------
| WEEKLY SALES
|--------------------------------------------------------------------------
*/


$weeklySales = $conn->query(

    $salesQuery . "

    AND YEARWEEK(
        p.payment_date
    )
    =
    YEARWEEK(
        CURDATE()
    )

    "

)->fetch_assoc()["total_sales"];






/*
|--------------------------------------------------------------------------
| MONTHLY SALES
|--------------------------------------------------------------------------
*/


$monthlySales = $conn->query(

    $salesQuery . "

    AND MONTH(
        p.payment_date
    )
    =
    MONTH(
        CURDATE()
    )

    AND YEAR(
        p.payment_date
    )
    =
    YEAR(
        CURDATE()
    )

    "

)->fetch_assoc()["total_sales"];







/*
|--------------------------------------------------------------------------
| DAILY SALES GRAPH
|--------------------------------------------------------------------------
*/


$dailySalesQuery = $conn->query("

SELECT


DATE(
    p.payment_date
)
AS sales_date,


SUM(

    tp.price -
    (
        tp.price *
        IFNULL(
            d.discount_percentage,
            0
        ) / 100
    )

)
AS total_sales


FROM booking_seats bs


INNER JOIN bookings b
ON bs.booking_id = b.booking_id


INNER JOIN payments p
ON b.booking_id = p.booking_id


INNER JOIN show_schedules ss
ON b.schedule_id = ss.schedule_id


INNER JOIN movies m
ON ss.movie_id = m.movie_id


INNER JOIN ticket_prices tp
ON m.movie_id = tp.movie_id


LEFT JOIN discounts d
ON bs.discount_id = d.discount_id


WHERE

p.payment_status = 'Paid'


GROUP BY

DATE(
    p.payment_date
)


ORDER BY

sales_date DESC


");






/*
|--------------------------------------------------------------------------
| MONTHLY SALES GRAPH
|--------------------------------------------------------------------------
*/


$monthlySalesQuery = $conn->query("

SELECT


DATE_FORMAT(
    p.payment_date,
    '%M %Y'
)
AS month,


SUM(

    tp.price -
    (
        tp.price *
        IFNULL(
            d.discount_percentage,
            0
        ) / 100
    )

)
AS total_sales


FROM booking_seats bs


INNER JOIN bookings b
ON bs.booking_id = b.booking_id


INNER JOIN payments p
ON b.booking_id = p.booking_id


INNER JOIN show_schedules ss
ON b.schedule_id = ss.schedule_id


INNER JOIN movies m
ON ss.movie_id = m.movie_id


INNER JOIN ticket_prices tp
ON m.movie_id = tp.movie_id


LEFT JOIN discounts d
ON bs.discount_id = d.discount_id


WHERE

p.payment_status='Paid'


GROUP BY

YEAR(
    p.payment_date
),

MONTH(
    p.payment_date
)


ORDER BY

YEAR(
    p.payment_date
),

MONTH(
    p.payment_date
)


");







/*
|--------------------------------------------------------------------------
| MOVIE SALES GRAPH
|--------------------------------------------------------------------------
*/


$movieSalesQuery = $conn->query("

SELECT


m.title,


SUM(

    tp.price -
    (
        tp.price *
        IFNULL(
            d.discount_percentage,
            0
        ) / 100
    )

)
AS total_sales,


COUNT(
    bs.seat_id
)
AS tickets_sold



FROM booking_seats bs


INNER JOIN bookings b
ON bs.booking_id = b.booking_id


INNER JOIN payments p
ON b.booking_id = p.booking_id


INNER JOIN show_schedules ss
ON b.schedule_id = ss.schedule_id


INNER JOIN movies m
ON ss.movie_id = m.movie_id


INNER JOIN ticket_prices tp
ON m.movie_id = tp.movie_id


LEFT JOIN discounts d
ON bs.discount_id = d.discount_id



WHERE

p.payment_status='Paid'


GROUP BY

m.title


ORDER BY

total_sales DESC


");







/*
|--------------------------------------------------------------------------
| PREPARE CHART DATA
|--------------------------------------------------------------------------
*/


$dailyLabels = [];
$dailyData = [];


while ($row = $dailySalesQuery->fetch_assoc()) {


    $dailyLabels[] =
        $row["sales_date"];


    $dailyData[] =
        $row["total_sales"];
}






$monthLabels = [];
$monthData = [];


while ($row = $monthlySalesQuery->fetch_assoc()) {


    $monthLabels[] =
        $row["month"];


    $monthData[] =
        $row["total_sales"];
}







$movieLabels = [];
$movieData = [];
$movieTicketData = [];


while ($row = $movieSalesQuery->fetch_assoc()) {


    $movieLabels[] =
        $row["title"];


    $movieData[] =
        $row["total_sales"];


    $movieTicketData[] =
        (int)$row["tickets_sold"];
}








/*
|--------------------------------------------------------------------------
| SALES RECORD TABLE
|--------------------------------------------------------------------------
*/


$result = $conn->query("

SELECT


DATE(
    p.payment_date
)
AS sales_date,


m.title AS movie_name,


COUNT(
    bs.seat_id
)
AS tickets,


SUM(

    tp.price -
    (
        tp.price *
        IFNULL(
            d.discount_percentage,
            0
        ) / 100
    )

)
AS total_sales



FROM booking_seats bs


INNER JOIN bookings b
ON bs.booking_id = b.booking_id


INNER JOIN payments p
ON b.booking_id = p.booking_id


INNER JOIN show_schedules ss
ON b.schedule_id = ss.schedule_id


INNER JOIN movies m
ON ss.movie_id = m.movie_id


INNER JOIN ticket_prices tp
ON m.movie_id = tp.movie_id


LEFT JOIN discounts d
ON bs.discount_id = d.discount_id



WHERE

p.payment_status='Paid'


GROUP BY

DATE(
    p.payment_date
),

m.title


ORDER BY

p.payment_date DESC


LIMIT 25


");
