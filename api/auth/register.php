<?php

header("Content-Type: application/json");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$first_name = trim($_POST["first_name"] ?? "");
$last_name = trim($_POST["last_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

if (
    $first_name === "" ||
    $last_name === "" ||
    $email === "" ||
    $password === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Please fill in all required fields."
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

if (strlen($password) < 8) {

    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 8 characters."
    ]);

    exit;
}

$userRepository = new UserRepository($conn);

$result = $userRepository->createUser(
    $first_name,
    $last_name,
    $email,
    $password,
    "Customer"
);

if (!$result["success"]) {
    echo json_encode($result);
    exit;
}

/*
|--------------------------------------------------------------------------
| Automatically log the newly registered user in
|--------------------------------------------------------------------------
*/

$user = $userRepository->getUserByEmail($email);
if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Unable to retrieve user after registration."
    ]);
    exit;
}
session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];
$_SESSION["fullname"] =
    $user["first_name"] . " " . $user["last_name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];

echo json_encode([
    "success" => true,
    "message" => "Account created successfully.",
    "user" => [
        "id" => $user["id"],
        "fullname" =>
        $user["first_name"] . " " . $user["last_name"],
        "email" => $user["email"],
        "role" => $user["role"]
    ]
]);

$conn->close();
