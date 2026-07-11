<?php

include("../../includes/db.php");

$sql = "SELECT ss.schedule_id, m.title, h.hall_name, h.total_seats, ss.ticket_price, TIME_FORMAT(ss.start_time,'%H:%i') start_time, ss.status,
    IFNULL(SUM(b.seats_booked),0) sold
    FROM show_schedules ss
    JOIN movies m
    ON ss.movie_id=m.movie_id
    JOIN cinema_halls h
    ON ss.hall_id=h.hall_id
    LEFT JOIN bookings b
    ON ss.schedule_id=b.schedule_id
    GROUP BY ss.schedule_id
    ORDER BY ss.show_date,start_time
";

$result = mysqli_query($conn, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $row["percent"] =
        ($row["sold"] / $row["total_seats"]) * 100;

    switch ($row["status"]) {
        case "Upcoming":
            $row["class"] = "upcoming";
            break;
        case "Now Showing":
            $row["class"] = "now";
            break;
        default:
            $row["class"] = "completed";
    }
    $data[] = $row;
};
echo json_encode($data);