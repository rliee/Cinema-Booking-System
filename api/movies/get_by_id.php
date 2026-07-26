<?php

require_once __DIR__ . "/../../includes/db.php";

header("Content-Type: application/json");


if (!isset($_GET["movie_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Movie ID required"
    ]);

    exit;
}


$movieId = intval($_GET["movie_id"]);



$sql = "
SELECT
    movies.movie_id,
    movies.title,
    movies.duration,
    movies.age_rating,
    movies.poster_url,
    movies.synopsis,
    genres.genre_name

FROM movies

INNER JOIN genres
ON movies.genre_id = genres.genre_id

WHERE movies.movie_id = ?
";



$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $movieId
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);



if ($movie = mysqli_fetch_assoc($result)) {


    echo json_encode([
        "success" => true,
        "movie" => $movie
    ]);
} else {


    echo json_encode([
        "success" => false,
        "message" => "Movie not found"
    ]);
}
