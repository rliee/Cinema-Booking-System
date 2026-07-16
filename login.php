<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["fullname"] = $user["fullname"];

            header("Location: homepage.html");
            exit();

        } else {
            echo "<script>
                    alert('Incorrect password!');
                    window.location='login.html';
                  </script>";
        }

    } else {
        echo "<script>
                alert('Email not found!');
                window.location='login.html';
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>