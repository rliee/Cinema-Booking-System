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

    public function findExistingUser(
        string $first_name,
        string $last_name,
        string $email
    ): ?string {
        // Exact account
        $statement = $this->conn->prepare("
        SELECT id
        FROM users
        WHERE first_name = ?
        AND last_name = ?
        AND email = ?
        LIMIT 1
    ");

        $statement->bind_param(
            "sss",
            $first_name,
            $last_name,
            $email
        );

        $statement->execute();

        if ($statement->get_result()->num_rows > 0) {

            $statement->close();

            return "account";
        }

        $statement->close();


        // Same first and last name
        $statement = $this->conn->prepare("
        SELECT id
        FROM users
        WHERE first_name = ?
        AND last_name = ?
        LIMIT 1
    ");

        $statement->bind_param(
            "ss",
            $first_name,
            $last_name
        );

        $statement->execute();

        if ($statement->get_result()->num_rows > 0) {

            $statement->close();

            return "person";
        }

        $statement->close();


        // Same email
        if ($this->emailExists($email)) {

            return "email";
        }

        return null;
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

        $existing = $this->findExistingUser(
            $first_name,
            $last_name,
            $email
        );

        if ($existing === "account") {
            return [
                "success" => false,
                "message" => "Account already exists. Please login instead."
            ];
        }

        if ($existing === "person") {
            return [
                "success" => false,
                "message" => "User already exists. Please login instead."
            ];
        }

        if ($existing === "email") {
            return [
                "success" => false,
                "message" => "This email is already registered."
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
