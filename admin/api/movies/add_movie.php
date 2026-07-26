<?php

header("Content-Type: application/json");


require_once __DIR__ . "/../../../includes/db.php";



try {


    if ($_SERVER["REQUEST_METHOD"] !== "POST") {

        echo json_encode([

            "success" => false,

            "message" => "Invalid request method."

        ]);

        exit;
    }



    /*
    |--------------------------------------------------------------------------
    | RECEIVE FORM DATA
    |--------------------------------------------------------------------------
    */


    $title =
        trim($_POST["title"] ?? "");


    $genreId =
        intval($_POST["genre_id"] ?? 0);


    $status =
        trim($_POST["status"] ?? "Now Showing");


    $duration =
        intval($_POST["duration"] ?? 0);


    $rating =
        trim($_POST["rating"] ?? "PG-13");


    $synopsis =
        trim($_POST["synopsis"] ?? "");


    $trailerUrl =
        trim($_POST["trailer_url"] ?? "");



    $posterUrl = "";





    if (
        empty($title) ||
        empty($genreId)
    ) {

        echo json_encode([

            "success" => false,

            "message" =>
            "Title and genre are required."

        ]);

        exit;
    }





    /*
    |--------------------------------------------------------------------------
    | POSTER UPLOAD
    |--------------------------------------------------------------------------
    */


    if (
        isset($_FILES["poster"]) &&
        $_FILES["poster"]["error"] === UPLOAD_ERR_OK
    ) {



        $file =
            $_FILES["poster"];



        $extension =
            strtolower(
                pathinfo(
                    $file["name"],
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


            echo json_encode([

                "success" => false,

                "message" =>
                "Invalid image format."

            ]);

            exit;
        }





        /*
        Actual folder
        */

        $uploadDirectory =
            __DIR__
            . "/../../../assets/images/poster/";



        if (
            !is_dir($uploadDirectory)
        ) {

            mkdir(
                $uploadDirectory,
                0755,
                true
            );
        }




        $fileName =
            uniqid("poster_")
            . "."
            . $extension;



        $destination =
            $uploadDirectory
            . $fileName;




        if (
            move_uploaded_file(
                $file["tmp_name"],
                $destination
            )
        ) {


            /*
            Store relative path
            */

            $posterUrl =
                "assets/images/poster/"
                . $fileName;
        }
    }





    /*
    |--------------------------------------------------------------------------
    | INSERT MOVIE
    |--------------------------------------------------------------------------
    */


    $sql = "

    INSERT INTO movies

    (
        title,
        genre_id,
        status,
        duration,
        age_rating,
        synopsis,
        poster_url,
        trailer_url
    )


    VALUES

    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )

    ";



    $stmt =
        $conn->prepare($sql);




    if (!$stmt) {


        throw new Exception(
            $conn->error
        );
    }




    $stmt->bind_param(

        "sisissss",

        $title,

        $genreId,

        $status,

        $duration,

        $rating,

        $synopsis,

        $posterUrl,

        $trailerUrl

    );





    if ($stmt->execute()) {


        echo json_encode([

            "success" => true,

            "message" =>
            "Movie added successfully."

        ]);
    } else {


        echo json_encode([

            "success" => false,

            "message" =>
            $stmt->error

        ]);
    }





    $stmt->close();

    $conn->close();
} catch (Exception $e) {


    echo json_encode([

        "success" => false,

        "message" =>
        "Server error: "
            . $e->getMessage()

    ]);
}
