<?php

class TransactionRepository
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function refundTransaction(int $bookingId): bool
    {
        $this->conn->begin_transaction();

        try {

            // Check current payment status
            $check = $this->conn->prepare("
            SELECT payment_status
            FROM payments
            WHERE booking_id = ?
        ");

            $check->bind_param("i", $bookingId);
            $check->execute();

            $result = $check->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Payment not found.");
            }

            $payment = $result->fetch_assoc();

            if ($payment["payment_status"] === "Refunded") {
                throw new Exception("Already refunded.");
            }

            // Update payment
            $update = $this->conn->prepare("
            UPDATE payments
            SET payment_status = 'Refunded'
            WHERE booking_id = ?
        ");

            $update->bind_param("i", $bookingId);
            $update->execute();

            // Free the booked seats
            $seatUpdate = $this->conn->prepare("
            UPDATE seats s
            INNER JOIN booking_seats bs
                ON s.seat_id = bs.seat_id
            SET s.status = 'Available'
            WHERE bs.booking_id = ?
        ");

            $seatUpdate->bind_param("i", $bookingId);
            $seatUpdate->execute();

            $this->conn->commit();

            return true;
        } catch (Exception $e) {

            $this->conn->rollback();

            return false;
        }
    }

    public function getTransactionHistoryByUserId(int $userId): array
    {
        $sql = "
            SELECT
                b.booking_id,
                b.booking_reference,
                ss.show_date,
                ss.start_time,

                m.title AS movie_title,

                h.hall_name AS cinema_hall,

                tp.price AS ticket_price,

                s.seat_label,

                p.payment_status AS status

            FROM bookings b

            INNER JOIN show_schedules ss
                ON b.schedule_id = ss.schedule_id

            INNER JOIN movies m
                ON ss.movie_id = m.movie_id

            INNER JOIN cinema_halls h
                ON ss.hall_id = h.hall_id

            INNER JOIN booking_seats bs
                ON b.booking_id = bs.booking_id

            INNER JOIN seats s
                ON bs.seat_id = s.seat_id

                INNER JOIN payments p
                ON p.booking_id = bs.booking_id

            LEFT JOIN ticket_prices tp
                ON m.movie_id = tp.movie_id

            WHERE b.user_id = ?

            ORDER BY 
                b.booking_date DESC,
                b.booking_id DESC
        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $userId
        );


        $stmt->execute();


        $result = $stmt->get_result();


        $transactions = [];


        while ($row = $result->fetch_assoc()) {

            $bookingId = $row["booking_id"];


            /*
                Create booking record
            */

            if (!isset($transactions[$bookingId])) {

                $transactions[$bookingId] = [

                    "booking_id" => $row["booking_id"],

                    "ticket_number" => $row["booking_reference"],

                    "movie_title" => $row["movie_title"],

                    "cinema_hall" => $row["cinema_hall"],

                    "show_date" => $row["show_date"],

                    "show_time" => $row["start_time"],

                    "seats" => [],

                    "total_price" => 0,

                    "status" => $row["status"]

                ];
            }


            /*
                Add seats into array
            */
            $transactions[$bookingId]["seats"][] =
                $row["seat_label"];


            /*
                Calculate total dynamically
            */
            $transactions[$bookingId]["total_price"] =
                ($row["ticket_price"] ?? 0)
                *
                count($transactions[$bookingId]["seats"]);
        }


        return array_values($transactions);
    }

    public function getTransactionHistory(): array
    {
        $sql = "
            SELECT
                b.booking_id,

                b.booking_reference,

                m.title AS movie_title,
                m.poster_url,

                h.hall_name AS cinema_hall,

                ss.show_date,

                ss.start_time AS show_time,


                GROUP_CONCAT(
                    bs.seat_label
                    ORDER BY bs.seat_label
                    SEPARATOR ', '
                ) AS seats,


                (
                    tp.price * COUNT(bs.seat_id)
                ) AS total_price,


                p.payment_status AS status


            FROM bookings b


            INNER JOIN show_schedules ss
                ON b.schedule_id = ss.schedule_id


            INNER JOIN movies m
                ON ss.movie_id = m.movie_id


            INNER JOIN cinema_halls h
                ON ss.hall_id = h.hall_id


            INNER JOIN booking_seats bs
                ON b.booking_id = bs.booking_id

            INNER JOIN payments p
                ON p.booking_id = bs.booking_id



            INNER JOIN ticket_prices tp
                ON m.movie_id = tp.movie_id


            GROUP BY
                b.booking_id


            ORDER BY
                b.booking_date DESC
        ";


        $result = $this->conn->query($sql);


        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // GROUP_CONCAT(
    //     s.seat_label
    //     ORDER BY s.seat_label
    //     SEPARATOR ', '
    // ) AS seats,

    // (
    //     tp.price * COUNT(s.seat_id)
    // ) AS total_price


    public function isRefunded(int $bookingId): bool
    {
        $sql = "
        SELECT 1
        FROM refund_history
        WHERE booking_id = ?
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function getRefundedTransactions(int $userId): array
    {
        $sql = "
        SELECT
            b.booking_id,
            b.booking_reference,

            p.payment_status AS status,

            m.title AS movie_title,
            m.poster_url,

            h.hall_name AS cinema_hall,

            ss.show_date,
            ss.start_time,

            s.seat_label,

            bs.discount_id,

            d.discount_name,
            d.discount_percentage,

            tp.price,

            rh.refund_id,
            rh.user_id AS refunded_by,
            rh.reason AS refund_reason,
            rh.refunded_at

        FROM bookings b

        INNER JOIN show_schedules ss
            ON b.schedule_id = ss.schedule_id

        INNER JOIN movies m
            ON ss.movie_id = m.movie_id

        INNER JOIN cinema_halls h
            ON ss.hall_id = h.hall_id

        INNER JOIN booking_seats bs
            ON b.booking_id = bs.booking_id

        INNER JOIN seats s
            ON bs.seat_id = s.seat_id

        LEFT JOIN discounts d
            ON bs.discount_id = d.discount_id

        LEFT JOIN ticket_prices tp
            ON m.movie_id = tp.movie_id

        INNER JOIN payments p
            ON p.booking_id = b.booking_id

        LEFT JOIN refund_history rh
            ON rh.booking_id = b.booking_id

        WHERE b.user_id = ?

        ORDER BY
            b.booking_date DESC,
            b.booking_id DESC,
            s.seat_label ASC
    ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getUserRefundedTransactions(int $userId): array
    {
        $sql = "
        SELECT
            b.booking_id,
            b.booking_reference,
          
            p.payment_status AS status,

            m.title AS movie_title,
            m.poster_url,

            h.hall_name AS cinema_hall,

            ss.show_date,
            ss.start_time,

            s.seat_label,

            bs.discount_id,

            d.discount_name,
            d.discount_percentage,

            tp.price

        FROM bookings b

        INNER JOIN show_schedules ss
            ON b.schedule_id = ss.schedule_id

        INNER JOIN movies m
            ON ss.movie_id = m.movie_id

        INNER JOIN cinema_halls h
            ON ss.hall_id = h.hall_id

        INNER JOIN booking_seats bs
            ON b.booking_id = bs.booking_id
            
        INNER JOIN seats s
            ON bs.seat_id = s.seat_id

        LEFT JOIN discounts d
            ON bs.discount_id = d.discount_id

        LEFT JOIN ticket_prices tp
            ON m.movie_id = tp.movie_id

        INNER JOIN payments p
            ON p.booking_id = bs.booking_id
        
        WHERE b.user_id = ?
        ORDER BY
            b.booking_date DESC,
            b.booking_id DESC,
            s.seat_label ASC
    ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $userId
        );


        $stmt->execute();


        $result = $stmt->get_result();


        $transactions = [];


        while ($row = $result->fetch_assoc()) {


            $bookingId = $row["booking_id"];


            if (!isset($transactions[$bookingId])) {


                $transactions[$bookingId] = [

                    "booking_id" => $bookingId,

                    "ticket_number" =>
                    $row["booking_reference"],

                    "movie_title" =>
                    $row["movie_title"],

                    "poster_url" =>
                    $row["poster_url"],

                    "cinema_hall" =>
                    $row["cinema_hall"],

                    "show_date" =>
                    $row["show_date"],

                    "show_time" =>
                    $row["start_time"],

                    "seats" => [],

                    "total_price" => 0,

                    "status" =>
                    $row["status"]

                ];
            }



            $seatPrice = $row["price"];



            if ($row["discount_id"]) {

                $discountAmount =
                    $seatPrice *
                    ($row["discount_percentage"] / 100);

                $finalPrice =
                    $seatPrice - $discountAmount;
            } else {

                $finalPrice = $seatPrice;
            }

            $transactions[$bookingId]["seats"][] = [

                "seat_label" =>
                $row["seat_label"],

                "discount_id" =>
                $row["discount_id"],

                "discount_name" =>
                $row["discount_name"],

                "price" =>
                $finalPrice

            ];

            $transactions[$bookingId]["total_price"]
                += $finalPrice;
        }


        return array_values($transactions);


        // return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserTransactions(int $userId): array
    {
        $sql = "
        SELECT
            b.booking_id,
            b.booking_reference,
          
            p.payment_status AS status,

            m.title AS movie_title,
            m.poster_url,

            h.hall_name AS cinema_hall,

            ss.show_date,
            ss.start_time,

            s.seat_label,

            bs.discount_id,

            d.discount_name,
            d.discount_percentage,

            tp.price

        FROM bookings b

        INNER JOIN show_schedules ss
            ON b.schedule_id = ss.schedule_id

        INNER JOIN movies m
            ON ss.movie_id = m.movie_id

        INNER JOIN cinema_halls h
            ON ss.hall_id = h.hall_id

        INNER JOIN booking_seats bs
            ON b.booking_id = bs.booking_id
            
        INNER JOIN seats s
            ON bs.seat_id = s.seat_id

        LEFT JOIN discounts d
            ON bs.discount_id = d.discount_id

        LEFT JOIN ticket_prices tp
            ON m.movie_id = tp.movie_id

        INNER JOIN payments p
            ON p.booking_id = bs.booking_id
        
        LEFT JOIN refund_history rh
            ON rh.booking_id = b.booking_id

        WHERE b.user_id = ?
        AND rh.booking_id IS NULL

        ORDER BY
            b.booking_date DESC,
            b.booking_id DESC,
            s.seat_label ASC
    ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $userId
        );


        $stmt->execute();


        $result = $stmt->get_result();


        $transactions = [];


        while ($row = $result->fetch_assoc()) {


            $bookingId = $row["booking_id"];


            if (!isset($transactions[$bookingId])) {


                $transactions[$bookingId] = [

                    "booking_id" => $bookingId,

                    "ticket_number" =>
                    $row["booking_reference"],

                    "movie_title" =>
                    $row["movie_title"],

                    "poster_url" =>
                    $row["poster_url"],

                    "cinema_hall" =>
                    $row["cinema_hall"],

                    "show_date" =>
                    $row["show_date"],

                    "show_time" =>
                    $row["start_time"],

                    "seats" => [],

                    "total_price" => 0,

                    "status" =>
                    $row["status"]

                ];
            }



            $seatPrice = $row["price"];



            if ($row["discount_id"]) {

                $discountAmount =
                    $seatPrice *
                    ($row["discount_percentage"] / 100);

                $finalPrice =
                    $seatPrice - $discountAmount;
            } else {

                $finalPrice = $seatPrice;
            }

            $transactions[$bookingId]["seats"][] = [

                "seat_label" =>
                $row["seat_label"],

                "discount_id" =>
                $row["discount_id"],

                "discount_name" =>
                $row["discount_name"],

                "price" =>
                $finalPrice

            ];

            $transactions[$bookingId]["total_price"]
                += $finalPrice;
        }


        return array_values($transactions);


        // return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getActiveTransactions(int $userId): array
    {
        $transactions =
            $this->getUserTransactions($userId);


        return array_filter(
            $transactions,
            function ($transaction) {

                return $transaction["status"] !== "Refunded";
            }
        );
    }



    public function getRefundedTransactionas(int $userId): array
    {
        $transactions =
            $this->getUserTransactions($userId);


        return array_filter(
            $transactions,
            function ($transaction) {

                return $transaction["status"] === "Refunded";
            }
        );
    }
}
