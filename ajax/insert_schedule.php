<?php

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate");

require_once "./includes/db.php";
require_once "../classes/ScheduleValidator.php";
require_once "../classes/ScheduleRepository.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

$data = [
    "movie_id"      => $_POST["movie_id"] ?? "",
    "hall_id"       => $_POST["hall_id"] ?? "",
    "show_date"     => $_POST["show_date"] ?? "",
    "start_time"    => $_POST["start_time"] ?? "",
    "ticket_price"  => $_POST["ticket_price"] ?? ""
];

$validator = new ScheduleValidator();
$errors = $validator->validate($data);

if (!empty($errors)) {
    echo json_encode([
        "success" => false,
        "message" => $errors
    ]);
    exit;
}

$repository = new ScheduleRepository($conn);
$response = $repository->insert($data);

echo json_encode($response);

// $response = [
//     "success" => false,
//     "message" => ""
// ];

// if (
//     empty($_POST["movie_id"]) ||
//     empty($_POST["hall_id"]) ||
//     empty($_POST["show_date"]) ||
//     empty($_POST["start_time"]) ||
//     empty($_POST["ticket_price"])
// ) {
//     $response["message"] = "Please complete all required fields.";
//     echo json_encode($response);
//     exit;
// }

// $movie_id = intval($_POST["movie_id"]);
// $hall_id = intval($_POST["hall_id"]);
// $show_date = $_POST["show_date"];
// $start_time = $_POST["start_time"];
// $ticket_price = $_POST["ticket_price"];

// $sql = "SELECT duration FROM movies WHERE movie_id=?";

// $stmt = mysqli_prepare($conn,$sql);

// mysqli_stmt_bind_param($stmt, "i", $movie_id);
// mysqli_stmt_execute($stmt);

// $result = mysqli_stmt_get_result($stmt);
// $movie = mysqli_fetch_assoc($result);

// if(!$movie){
//     $response["message"]="Movie not found.";
//     echo json_encode($response);
//     exit;
// }
// $duration = $movie["duration"];

// $start = new DateTime($start_time);
// $end = clone $start;
// $end->modify("+{$duration} minutes");
// $end_time = $end->format("H:i:s");

// $sql = "SELECT schedule_id
// FROM show_schedules
// WHERE hall_id=? AND show_date=? AND(start_time < ? AND end_time > ?)";

// $stmt = mysqli_prepare($conn,$sql);
// mysqli_stmt_bind_param($stmt, "isss", $hall_id, $show_date, $end_time, $start_time);
// mysqli_stmt_execute($stmt);
// $result=mysqli_stmt_get_result($stmt);

// if(mysqli_num_rows($result)>0){
//     $response["message"]="Schedule conflict detected for this cinema hall.";
//     echo json_encode($response);
//     exit;
// }

// $sql = "INSERT INTO show_schedules(movie_id, hall_id, show_date, start_time, end_time, ticket_price) VALUES(?,?,?,?,?,?)";

// $stmt = mysqli_prepare($conn,$sql);
// mysqli_stmt_bind_param($stmt, "iisssd", $movie_id, $hall_id, $show_date, $start_time, $end_time, $ticket_price);

// if(mysqli_stmt_execute($stmt)){
//     $response["success"]=true;
//     $response["message"]="Schedule created.";
// } else{
//     $response["message"]="Database error.";
// }
// echo json_encode($response);