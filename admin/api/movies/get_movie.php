<?php


header(
    "Content-Type: application/json; charset=utf-8"
);



require_once __DIR__ . "/../../../includes/db.php";



mysqli_report(
    MYSQLI_REPORT_ERROR |
        MYSQLI_REPORT_STRICT
);



function jsonResponse(
    bool $success,
    $data = null
): void {


    echo json_encode([

        "success" => $success,

        $success ? "data" : "error"
        =>
        $data

    ]);


    exit;
}




try {


    if (
        !isset($conn)
        ||
        $conn->connect_error
    ) {


        throw new Exception(
            "Database connection failed."
        );
    }





    /*
    |--------------------------------------------------------------------------
    | GET MOVIES WITH GENRE
    |--------------------------------------------------------------------------
    */


    $sql = "

        SELECT

            m.movie_id,

            m.title,

            m.duration,

            m.age_rating,

            m.status,

            m.synopsis,

            m.poster_url,

            m.trailer_url,


            g.genre_name


        FROM movies m


        LEFT JOIN genres g

            ON m.genre_id = g.genre_id


        ORDER BY

            m.movie_id DESC

    ";



    $result =
        $conn->query($sql);





    $movies = [];



    while (
        $row =
        $result->fetch_assoc()
    ) {


        $movies[] = [

            "movie_id" =>
            $row["movie_id"],


            "title" =>
            $row["title"],


            "genre_name" =>
            $row["genre_name"]
                ??
                "Uncategorized",


            "duration" =>
            (int)$row["duration"],


            "age_rating" =>
            $row["age_rating"]
                ??
                "PG-13",


            "status" =>
            $row["status"],


            "synopsis" =>
            $row["synopsis"]
                ??
                "",


            "poster_url" =>
            $row["poster_url"]
                ??
                "",


            "trailer_url" =>
            $row["trailer_url"]
                ??
                ""

        ];
    }





    $conn->close();



    jsonResponse(
        true,
        $movies
    );
} catch (Exception $e) {


    jsonResponse(
        false,
        $e->getMessage()
    );
}
