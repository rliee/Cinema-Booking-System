<?php


header("Content-Type: application/json");


require_once __DIR__ . "/../../../includes/db.php";
 



try {


    if ($_SERVER["REQUEST_METHOD"] !== "POST") {


        echo json_encode([

            "success" => false,

            "message" =>
            "Invalid request method."

        ]);

        exit;
    }



    $movieId =
        $_POST["id"] ?? null;



    if (!$movieId) {


        echo json_encode([

            "success" => false,

            "message" =>
            "Movie ID is required."

        ]);


        exit;
    }





    /*
    |--------------------------------------------------------------------------
    | GET POSTER PATH
    |--------------------------------------------------------------------------
    */


    $posterQuery = $conn->prepare("

        SELECT poster_url

        FROM movies

        WHERE movie_id = ?

    ");




    if ($posterQuery) {


        $posterQuery->bind_param(

            "i",

            $movieId

        );


        $posterQuery->execute();


        $result =
            $posterQuery->get_result();



        if ($row = $result->fetch_assoc()) {


            $poster =
                $row["poster_url"];



            if (!empty($poster)) {


                /*
                Convert database path:

                assets/images/poster/a.jpg

                into:

                C:\xampp\htdocs\Cinema-Booking-System
                \assets\images\poster\a.jpg
                */


                $filePath =
                    __DIR__
                    . "/../../"
                    . $poster;



                if (
                    file_exists($filePath)
                ) {

                    unlink($filePath);
                }
            }
        }



        $posterQuery->close();
    }





    /*
    |--------------------------------------------------------------------------
    | DELETE MOVIE RECORD
    |--------------------------------------------------------------------------
    */


    $stmt =
        $conn->prepare("

        DELETE FROM movies

        WHERE movie_id = ?

    ");




    if (!$stmt) {


        throw new Exception(
            $conn->error
        );
    }




    $stmt->bind_param(

        "i",

        $movieId

    );





    if ($stmt->execute()) {


        if ($stmt->affected_rows > 0) {


            echo json_encode([

                "success" => true,

                "message" =>
                "Movie deleted successfully."

            ]);
        } else {


            echo json_encode([

                "success" => false,

                "message" =>
                "Movie not found."

            ]);
        }
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
