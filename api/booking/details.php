<?php

require_once __DIR__ . "/../booking/details.php";

header("Content-Type: application/json");

if (!isset($_GET["movie"])) {
    echo json_encode([
        "success" => false,
        "message" => "Movie not specified."
    ]);
    exit;
}

$movie = trim($_GET["movie"]);

$sql = "
SELECT
    ss.schedule_id,
    ss.show_date,
    ss.start_time,
    ss.end_time,

    m.movie_id,
    m.title,
    m.poster_url,
    m.duration,
    m.age_rating,
    m.synopsis,

    h.hall_name,

    tp.price

FROM show_schedules ss

INNER JOIN movies m
ON ss.movie_id = m.movie_id

INNER JOIN cinema_halls h
ON ss.hall_id = h.hall_id

LEFT JOIN ticket_prices tp
ON tp.movie_id = m.movie_id

WHERE
    m.title = ?
AND
    ss.show_date >= CURDATE()

ORDER BY
    ss.show_date,
    ss.start_time
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $movie);

$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);
