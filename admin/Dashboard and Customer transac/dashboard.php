<?php

require_once __DIR__ . "/customer-transaction-php/connection.php";



/*
|--------------------------------------------------------------------------
| TODAY REVENUE
|--------------------------------------------------------------------------
|
| Dynamic:
| ticket price - discount
|
| Only Paid payments
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
) AS today_revenue


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

AND DATE(
    p.payment_date
)=CURDATE()

";


$todayRevenue =
    $conn->query($revenueQuery)
        ->fetch_assoc()["today_revenue"];







/*
|--------------------------------------------------------------------------
| TOTAL TICKETS SOLD
|--------------------------------------------------------------------------
*/


$ticketQuery = "

SELECT

COUNT(bs.seat_id) AS ticket


FROM booking_seats bs


INNER JOIN bookings b
ON bs.booking_id = b.booking_id


INNER JOIN payments p
ON b.booking_id = p.booking_id


WHERE

p.payment_status='Paid'


";


$totalTickets =
    $conn->query($ticketQuery)
        ->fetch_assoc()["ticket"];







/*
|--------------------------------------------------------------------------
| SHOWING MOVIES
|--------------------------------------------------------------------------
*/


$movieQuery = "

SELECT

COUNT(*) AS movies


FROM movies


WHERE

status='Now Showing'


";


$totalMovies =
    $conn->query($movieQuery)
        ->fetch_assoc()["movies"];








/*
|--------------------------------------------------------------------------
| TODAY'S SCREENINGS
|--------------------------------------------------------------------------
*/


$screeningQuery = "

SELECT

COUNT(*) AS screenings


FROM show_schedules


WHERE

show_date = CURDATE()


";


$todayScreenings =
    $conn->query($screeningQuery)
        ->fetch_assoc()["screenings"];








/*
|--------------------------------------------------------------------------
| RECENT TRANSACTIONS
|--------------------------------------------------------------------------
*/


$recentQuery = "

SELECT


CONCAT(
    u.first_name,
    ' ',
    u.last_name
) AS customer_name,


m.title AS movie_title,


COUNT(bs.seat_id) AS total_tickets,


SUM(

    tp.price -
    (
        tp.price *
        IFNULL(
            d.discount_percentage,
            0
        ) / 100
    )

) AS total_amount



FROM bookings b


INNER JOIN users u
ON b.user_id = u.id


INNER JOIN booking_seats bs
ON b.booking_id = bs.booking_id


INNER JOIN show_schedules ss
ON b.schedule_id = ss.schedule_id


INNER JOIN movies m
ON ss.movie_id = m.movie_id


INNER JOIN ticket_prices tp
ON m.movie_id = tp.movie_id


LEFT JOIN discounts d
ON bs.discount_id = d.discount_id


INNER JOIN payments p
ON b.booking_id = p.booking_id



WHERE

p.payment_status='Paid'


GROUP BY

b.booking_id


ORDER BY

b.booking_date DESC


LIMIT 5


";


$recentTransactions =
    $conn->query($recentQuery);







/*
|--------------------------------------------------------------------------
| TODAY'S SCHEDULE
|--------------------------------------------------------------------------
*/


$scheduleQuery = "

SELECT


m.title AS movie_title,


ss.start_time



FROM show_schedules ss


INNER JOIN movies m

ON ss.movie_id = m.movie_id



WHERE

ss.show_date = CURDATE()



ORDER BY

ss.start_time ASC


";


$schedules =
    $conn->query($scheduleQuery);
