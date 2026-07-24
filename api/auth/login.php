<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../auth/session.php";
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../classes/UserRepository.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($email === "" || $password === "") {

    echo json_encode([
        "success" => false,
        "message" => "Please enter your email and password."
    ]);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}

$userRepository = new UserRepository($conn);

$user = $userRepository->getUserByEmail($email);

if (!$user) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);

    exit;
}

if (!password_verify($password, $user["password"])) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Login Successful
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);

$_SESSION["id"] = $user["id"];
$_SESSION["fullname"] =
    $user["first_name"] . " " . $user["last_name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];

echo json_encode([
    "success" => true,
    "message" => "Login successful.",
    "user" => [
        "id" => $user["id"],
        "fullname" =>
            $user["first_name"] . " " . $user["last_name"],
        "email" => $user["email"],
        "role" => $user["role"]
    ]
]);

$conn->close();