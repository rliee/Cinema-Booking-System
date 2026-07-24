<?php

class UserRepository
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Check whether an email already exists.
     */
    public function emailExists(string $email): bool
    {
        $statement = $this->conn->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        if (!$statement) {
            return false;
        }

        $statement->bind_param("s", $email);
        $statement->execute();

        $exists = $statement
            ->get_result()
            ->num_rows > 0;

        $statement->close();

        return $exists;
    }

    /**
     * Get a user by email.
     */
    public function getUserByEmail(string $email): ?array
    {
        $statement = $this->conn->prepare("
            SELECT
                id,
                first_name,
                last_name,
                email,
                password,
                role,
                created_at,
                updated_at
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        if (!$statement) {
            return null;
        }

        $statement->bind_param("s", $email);
        $statement->execute();

        $user = $statement
            ->get_result()
            ->fetch_assoc();

        $statement->close();

        return $user ?: null;
    }

    /**
     * Get a user by ID.
     */
    public function getUserById(int $id): ?array
    {
        $statement = $this->conn->prepare("
            SELECT
                id,
                first_name,
                last_name,
                email,
                role,
                created_at,
                updated_at
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        if (!$statement) {
            return null;
        }

        $statement->bind_param("i", $id);
        $statement->execute();

        $user = $statement
            ->get_result()
            ->fetch_assoc();

        $statement->close();

        return $user ?: null;
    }

    /**
     * Create a new user.
     */
    public function createUser(
        string $first_name,
        string $last_name,
        string $email,
        string $password,
        string $role,
    ): array {

        if ($this->emailExists($email)) {

            return [
                "success" => false,
                "message" => "Email already exists."
            ];
        }

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $statement = $this->conn->prepare("
            INSERT INTO users
            (
                first_name,
                last_name,
                email,
                password,
                role
            )
            VALUES
            (
                ?, ?, ?, ?, ?
            )
        ");

        if (!$statement) {

            return [
                "success" => false,
                "message" => "Failed to prepare statement."
            ];
        }

        $statement->bind_param(
            "sssss",
            $first_name,
            $last_name,
            $email,
            $hashedPassword,
            $role
        );

        if (!$statement->execute()) {

            $statement->close();

            return [
                "success" => false,
                "message" => "Failed to create account."
            ];
        }

        $statement->close();

        return [
            "success" => true,
            "message" => "Account created successfully."
        ];
    }
}