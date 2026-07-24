<?php

// require_once __DIR__ . "/../includes/db.php";
// require_once __DIR__ . "/../classes/UserRepository.php";

// function build_response(string $message, bool $success = false)
// {
//     return json_encode([
//         "success" => $success,
//         "message" => $message
//     ]);
// }

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $userRepository = new UserRepository($conn);

//     $fullname = $_POST["fullname"];
//     $email = $_POST["email"];
//     $password = $_POST["password"];

//     if (empty($fullname) || empty($email) || empty($password)) {
//         return build_response("Invalid required fields.");
//     }

//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         return build_response("Invalid email.");
//     }

//     if(strlen($fullname) <= 7 || strlen($password) <= 4) {
//         return build_response("Invalid arguments.");
//     }

//     $result = $userRepository->insert($fullname, $email, $password);

//     return build_response("Registered successfully.", $result);

// }

// $conn->close();
