<?php

class UserRepository
{
    private mysqli $conn;

    // -- constructor --
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function findByEmail(string $email)
    {
        $sql = "SELECT id, fullname, email, password FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function insert(string $fullname, string $email, string $password)
    {
        $sql = "INSERT INTO users (fullname, email, password) VALUES (?,?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $fullname, $email, $password);
        $stmt->execute();
        return $stmt->insert_id;
    }
}
