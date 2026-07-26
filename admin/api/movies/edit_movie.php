<?php


header("Content-Type: application/json");


require_once __DIR__ . "/../../../includes/db.php";



mysqli_report(
    MYSQLI_REPORT_ERROR |
        MYSQLI_REPORT_STRICT
);



function jsonResponse(
    bool $success,
    ?string $message = null
): void {

    echo json_encode([

        "success" => $success,

        "message" => $message

    ]);

    exit;
}



try {


    if ($_SERVER["REQUEST_METHOD"] !== "POST") {


        jsonResponse(
            false,
            "Invalid request method."
        );
    }



    /*
    |--------------------------------------------------------------------------
    | GET FORM DATA
    |--------------------------------------------------------------------------
    */


    $movieId =
        intval(
            $_POST["id"] ?? 0
        );


    $title =
        trim(
            $_POST["title"] ?? ""
        );


    $genreId =
        intval(
            $_POST["genre_id"] ?? 0
        );


    $duration =
        intval(
            $_POST["duration"] ?? 0
        );


    $rating =
        trim(
            $_POST["rating"] ?? ""
        );


    $status =
        trim(
            $_POST["status"] ?? "Now Showing"
        );


    $synopsis =
        trim(
            $_POST["synopsis"] ?? ""
        );


    $trailerUrl =
        trim(
            $_POST["trailer_url"] ?? ""
        );




    if (
        !$movieId ||
        !$title ||
        !$genreId
    ) {


        jsonResponse(
            false,
            "Movie ID, title and genre are required."
        );
    }





    /*
    |--------------------------------------------------------------------------
    | GET CURRENT POSTER
    |--------------------------------------------------------------------------
    */


    $posterUrl = "";



    $stmt =
        $conn->prepare("

        SELECT poster_url

        FROM movies

        WHERE movie_id = ?

    ");



    $stmt->bind_param(
        "i",
        $movieId
    );


    $stmt->execute();


    $result =
        $stmt->get_result();



    if ($row = $result->fetch_assoc()) {


        $posterUrl =
            $row["poster_url"];
    }



    $stmt->close();





    /*
    |--------------------------------------------------------------------------
    | HANDLE NEW POSTER
    |--------------------------------------------------------------------------
    */


    if (
        isset($_FILES["poster"])
        &&
        $_FILES["poster"]["error"]
        === UPLOAD_ERR_OK
    ) {


        $extension =
            strtolower(
                pathinfo(
                    $_FILES["poster"]["name"],
                    PATHINFO_EXTENSION
                )
            );



        $allowed = [

            "jpg",
            "jpeg",
            "png",
            "webp"

        ];



        if (
            !in_array(
                $extension,
                $allowed
            )
        ) {


            jsonResponse(
                false,
                "Invalid image format."
            );
        }




        $uploadDir =
            __DIR__
            . "/../../assets/images/posters/";



        if (
            !is_dir($uploadDir)
        ) {

            mkdir(
                $uploadDir,
                0755,
                true
            );
        }




        $fileName =
            uniqid()
            . "."
            . $extension;



        $destination =
            $uploadDir
            . $fileName;



        if (
            move_uploaded_file(
                $_FILES["poster"]["tmp_name"],
                $destination
            )
        ) {


            $posterUrl =
                "assets/images/posters/"
                . $fileName;
        }
    }





    /*
    |--------------------------------------------------------------------------
    | UPDATE MOVIE
    |--------------------------------------------------------------------------
    */


    $stmt =
        $conn->prepare("

        UPDATE movies

        SET

            title = ?,

            genre_id = ?,

            status = ?,

            duration = ?,

            age_rating = ?,

            synopsis = ?,

            poster_url = ?,

            trailer_url = ?


        WHERE movie_id = ?

    ");




    $stmt->bind_param(

        "sisissssi",

        $title,

        $genreId,

        $status,

        $duration,

        $rating,

        $synopsis,

        $posterUrl,

        $trailerUrl,

        $movieId

    );



    $stmt->execute();



    jsonResponse(
        true,
        "Movie updated successfully."
    );
} catch (Exception $e) {


    jsonResponse(
        false,
        $e->getMessage()
    );
}
