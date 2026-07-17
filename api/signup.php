<?php
include "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check if the email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email is already registered!');
                window.location='signup.html';
              </script>";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Save the user
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>
                alert('Account created successfully!');
                window.location='login.html';
              </script>";
    } else {
        echo "<script>
                alert('Something went wrong!');
                window.location='signup.html';
              </script>";
    }

    $stmt->close();
    $check->close();
}

$conn->close();
