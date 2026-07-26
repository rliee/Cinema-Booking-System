<?php

require_once __DIR__ . "/../../includes/db.php";

header("Content-Type: application/json");


$sql = "
    SELECT
        m.movie_id,
        m.title,
        m.duration,
        m.age_rating,
        m.poster_url,
        g.genre_name
    FROM movies m
    INNER JOIN genres g
    ON m.genre_id = g.genre_id
    WHERE m.status = 'Now Showing'
    ORDER BY m.created_at DESC
";

$result = mysqli_query($conn, $sql);


if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

    exit;
}


$movies = [];


while ($row = mysqli_fetch_assoc($result)) {

    $movies[] = $row;
}


echo json_encode([
    "success" => true,
    "movies" => $movies
]);
