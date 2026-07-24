<?php

class TicketPricingRepository
{
    private mysqli $conn;

    public function __construct(mysqli $connection)
    {
        $this->conn = $connection;
    }

    public function getTicketPrices(): array
    {
        $sql = "SELECT
                tp.price_id,
                tp.movie_id,
                m.title,
                tp.price,
                tp.created_at,
                tp.updated_at
            FROM ticket_prices tp
            INNER JOIN movies m
                ON tp.movie_id = m.movie_id
            ORDER BY m.title ASC
        ";

        $result = $this->conn->query($sql);

        if (!$result) {
            return [];
        }

        $prices = [];

        while ($row = $result->fetch_assoc()) {
            $prices[] = $row;
        }

        return $prices;
    }

    public function getByScheduleId(int $scheduleId): ?array
    {
        $sql = "
        SELECT
            tp.*,
            m.title,
            ss.schedule_id
        FROM show_schedules ss
        INNER JOIN ticket_prices tp
            ON ss.movie_id = tp.movie_id
        INNER JOIN movies m
            ON ss.movie_id = m.movie_id
        WHERE ss.schedule_id = ?
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $scheduleId);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }

    public function getDiscounts(): array
    {
        $sql = "SELECT
                discount_id,
                discount_name,
                discount_percentage,
                created_at,
                updated_at
            FROM discounts
            ORDER BY discount_name ASC
        ";

        $result = $this->conn->query($sql);

        if (!$result) {
            return [];
        }

        $discounts = [];

        while ($row = $result->fetch_assoc()) {
            $discounts[] = $row;
        }

        return $discounts;
    }

    public function getAvailableMovies(): array
    {
        $sql = "SELECT
                m.movie_id,
                m.title
            FROM movies m
            LEFT JOIN ticket_prices tp
                ON m.movie_id = tp.movie_id
            WHERE tp.movie_id IS NULL
            ORDER BY m.title ASC
        ";

        $result = $this->conn->query($sql);

        if (!$result) {
            return [];
        }

        $movies = [];

        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }

        return $movies;
    }

    public function insertTicketPrice(int $movieId, float $price): array
    {
        $check = $this->conn->prepare("SELECT COUNT(*)
            FROM ticket_prices
            WHERE movie_id = ?
        ");

        if (!$check) {
            return [
                "success" => false,
                "message" => "Failed to prepare duplicate check."
            ];
        }

        $check->bind_param("i", $movieId);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            return [
                "success" => false,
                "message" => "This movie already has a ticket price."
            ];
        }

        $sql = "INSERT INTO ticket_prices (movie_id, price) VALUES (?, ?)";
        $statement = $this->conn->prepare($sql);

        if (!$statement) {
            return [
                "success" => false,
                "message" => "Failed to prepare statement."
            ];
        }

        $statement->bind_param("id", $movieId, $price);

        if (!$statement->execute()) {
            $statement->close();
            return [
                "success" => false,
                "message" => "Failed to save ticket price."
            ];
        }

        $statement->close();

        return [
            "success" => true,
            "message" => "Ticket price added successfully."
        ];
    }

    public function getTicketPriceById(int $priceId): ?array
    {
        $sql = "SELECT
                        tp.price_id,
                        tp.movie_id,
                        m.title,
                        tp.price
                    FROM ticket_prices tp
                    INNER JOIN movies m
                        ON tp.movie_id = m.movie_id
                    WHERE tp.price_id = ?";

        $statement = $this->conn->prepare($sql);

        if (!$statement) {
            return null;
        }

        $statement->bind_param("i", $priceId);

        if (!$statement->execute()) {
            $statement->close();
            return null;
        }

        $result = $statement->get_result();

        $ticketPrice = $result->fetch_assoc();

        $statement->close();

        return $ticketPrice ?: null;
    }

    public function updateTicketPrice(int $priceId, float $price): array
    {
        $current = $this->getTicketPriceById($priceId);

        if (!$current) {
            return [
                "success" => false,
                "message" => "Ticket price not found."
            ];
        }

        if ((float) $current["price"] === $price) {
            return [
                "success" => false,
                "message" => "No changes were made."
            ];
        }

        $sql = "UPDATE ticket_prices
            SET
                price = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE price_id = ?
        ";

        $statement = $this->conn->prepare($sql);

        if (!$statement) {
            return [
                "success" => false,
                "message" => "Failed to prepare statement."
            ];
        }

        $statement->bind_param("di", $price, $priceId);

        if (!$statement->execute()) {

            $statement->close();

            return [
                "success" => false,
                "message" => "Failed to update ticket price."
            ];
        }

        $statement->close();

        return [
            "success" => true,
            "message" => "Ticket price updated successfully."
        ];
    }

    public function deleteTicketPrice(int $priceId): array
    {
        $sql = "DELETE FROM ticket_prices
                WHERE price_id = ?";

        $statement = $this->conn->prepare($sql);

        if (!$statement) {
            return [
                "success" => false,
                "message" => "Failed to prepare statement."
            ];
        }

        $statement->bind_param("i", $priceId);

        if (!$statement->execute()) {
            $statement->close();

            return [
                "success" => false,
                "message" => "Failed to delete ticket price."
            ];
        }

        $statement->close();

        return [
            "success" => true,
            "message" => "Ticket price deleted successfully."
        ];
    }

    public function insertDiscount(string $discountName, float $discountPercentage): array
    {

        $check = $this->conn->prepare(
            "SELECT COUNT(*)
            FROM discounts
            WHERE discount_name = ?"
        );

        if (!$check) {
            return [
                "success" => false,
                "message" => "Failed to prepare duplicate check."
            ];
        }

        $check->bind_param("s", $discountName);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            return [
                "success" => false,
                "message" => "This discount already exists."
            ];
        }

        $statement = $this->conn->prepare(
            "INSERT INTO discounts
            (
                discount_name,
                discount_percentage
            )
            VALUES
            (?, ?)"
        );

        if (!$statement) {
            return [
                "success" => false,
                "message" => "Failed to prepare statement."
            ];
        }

        $statement->bind_param(
            "sd",
            $discountName,
            $discountPercentage
        );

        if (!$statement->execute()) {

            $statement->close();

            return [
                "success" => false,
                "message" => "Failed to add discount."
            ];
        }

        $statement->close();

        return [
            "success" => true,
            "message" => "Discount added successfully."
        ];
    }

    public function getDiscountById(int $discountId): ?array
    {
        $sql = "SELECT
                    discount_id,
                    discount_name,
                    discount_percentage
                FROM discounts
                WHERE discount_id = ?";

        $statement = $this->conn->prepare($sql);

        if (!$statement) {
            return null;
        }

        $statement->bind_param("i", $discountId);

        if (!$statement->execute()) {
            $statement->close();
            return null;
        }

        $result = $statement->get_result();

        $discount = $result->fetch_assoc();

        $statement->close();

        return $discount ?: null;
    }

    public function updateDiscount(
        int $discountId,
        float $discountPercentage
    ): array {

        $current = $this->getDiscountById($discountId);

        if (!$current) {
            return [
                "success" => false,
                "message" => "Discount not found."
            ];
        }

        if ((float) $current["discount_percentage"] === $discountPercentage) {
            return [
                "success" => false,
                "message" => "No changes were made."
            ];
        }    

        $statement = $this->conn->prepare(
            "UPDATE discounts
            SET
                discount_percentage = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE discount_id = ?"
        );

        if (!$statement) {
            return [
                "success" => false,
                "message" => "Failed to prepare statement."
            ];
        }

        $statement->bind_param(
            "di",
            $discountPercentage,
            $discountId
        );

        if (!$statement->execute()) {

            $statement->close();

            return [
                "success" => false,
                "message" => "Failed to update discount."
            ];
        }

        $statement->close();

        return [
            "success" => true,
            "message" => "Discount updated successfully."
        ];
    }

    public function deleteDiscount(int $discountId): array
    {
        $statement = $this->conn->prepare(
            "DELETE FROM discounts
            WHERE discount_id = ?"
        );

        if (!$statement) {
            return [
                "success" => false,
                "message" => "Failed to prepare statement."
            ];
        }

        $statement->bind_param("i", $discountId);

        if (!$statement->execute()) {

            $statement->close();

            return [
                "success" => false,
                "message" => "Failed to delete discount."
            ];
        }

        $statement->close();

        return [
            "success" => true,
            "message" => "Discount deleted successfully."
        ];
    }
}
