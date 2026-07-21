<?php
session_start();

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../classes/UserRepository.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $userRepository = new UserRepository($conn);
    $user = $userRepository->findByEmail($email);

    if (password_verify($password, $user["password"])) {

        // $_SESSION["user_id"] = $user["id"];
        // $_SESSION["fullname"] = $user["fullname"];

        return json_encode([
            "success" => true,
            "id" => $user["id"]
        ]);
    } 

    return json_encode([
        "success" => false,
    ]);
}

$conn->close();
