<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Authentication Helpers
|--------------------------------------------------------------------------
| Used only by PHP pages.
| This file should NEVER return JSON.
*/

function isLoggedIn(): bool
{
    return isset($_SESSION["user_id"]);
}

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        "user_id"  => $_SESSION["user_id"],
        "fullname" => $_SESSION["fullname"],
        "email"    => $_SESSION["email"],
        "role"     => $_SESSION["role"]
    ];
}

function currentUserId(): ?int
{
    return $_SESSION["user_id"] ?? null;
}

function currentUserRole(): ?string
{
    return $_SESSION["role"] ?? null;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {

        header("Location: /index.php");
        exit;
    }
}

function requireAdmin(): void
{
    requireLogin();

    if (currentUserRole() !== "Admin") {

        http_response_code(403);
        exit("Access denied.");
    }
}

function logout(): void
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}