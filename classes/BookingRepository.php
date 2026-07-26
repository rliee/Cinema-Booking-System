<?php

class BookingRepository
{
    private mysqli $conn;


    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE BOOKING
    |--------------------------------------------------------------------------
    */


    public function getPaymentByBookingReference(string $bookingReference)
    {
        $sql = "
        SELECT
            p.payment_id,
            p.payment_status,

            b.booking_id,
            b.booking_reference,
            b.schedule_id,
            b.user_id,
            b.booking_date

        FROM payments p

        INNER JOIN bookings b
            ON p.booking_id = b.booking_id

        WHERE b.booking_reference = ?
    ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "s",
            $bookingReference
        );


        $stmt->execute();


        $result = $stmt->get_result();


        return $result->fetch_assoc();
    }

    public function createBooking(
        int $userId,
        int $scheduleId,
        array $seats
    ): string|false {


        $bookingReference =
            "TKT-" . strtoupper(substr(uniqid(), -8));


        /*
        Create booking
    */
        $sql = "
        INSERT INTO bookings
        (
            user_id,
            booking_reference,
            schedule_id,
            booking_date,
            booking_status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            NOW(),
            'Pending'
        )
    ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "isi",
            $userId,
            $bookingReference,
            $scheduleId
        );


        if (!$stmt->execute()) {
            return false;
        }


        $bookingId = $this->conn->insert_id;



        /*
        Insert booking seats
    */
        $seatSQL = "
        INSERT INTO booking_seats
        (
            booking_id,
            seat_id,
            discount_id
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ";


        $seatStmt = $this->conn->prepare($seatSQL);



        foreach ($seats as $seat) {


            $seatId = $seat["seat_id"];

            $discountId =
                $seat["discount_id"] ?? null;



            $seatStmt->bind_param(
                "iii",
                $bookingId,
                $seatId,
                $discountId
            );


            if (!$seatStmt->execute()) {

                return false;
            }
        }


        return $bookingReference;
    }



    /*
    |--------------------------------------------------------------------------
    | GENERATE BOOKING REFERENCE
    |--------------------------------------------------------------------------
    */

    public function generateBookingReference(): string
    {
        return "TKT-" . strtoupper(substr(md5(uniqid()), 0, 8));
    }



    /*
    |--------------------------------------------------------------------------
    | GET BOOKING BY ID
    |--------------------------------------------------------------------------
    */

    public function getBookingById(int $bookingId): ?array
    {

        $sql = "
            SELECT
                *
            FROM bookings
            WHERE booking_id = ?
        ";


        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $bookingId
        );


        $stmt->execute();


        $result = $stmt->get_result();


        return $result->fetch_assoc() ?: null;
    }



    /*
    |--------------------------------------------------------------------------
    | GET CUSTOMER TRANSACTION HISTORY
    |--------------------------------------------------------------------------
    */
    public function getCustomerTransactions(): array
    {
        $sql = "
        SELECT
            b.booking_id,
            b.booking_reference,

            m.title AS movie_title,

            h.hall_name AS cinema_hall,

            ss.show_date,
            ss.start_time AS show_time,

            GROUP_CONCAT(
                bs.seat_label
                ORDER BY bs.seat_label
                SEPARATOR ', '
            ) AS seats,

            (tp.price * COUNT(bs.seat_id)) AS total_price,

            b.booking_status AS status

        FROM bookings b

        INNER JOIN show_schedules ss
            ON b.schedule_id = ss.schedule_id

        INNER JOIN movies m
            ON ss.movie_id = m.movie_id

        INNER JOIN cinema_halls h
            ON ss.hall_id = h.hall_id

        INNER JOIN booking_seats bs
            ON b.booking_id = bs.booking_id

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


    /*
    |--------------------------------------------------------------------------
    | UPDATE BOOKING STATUS
    |--------------------------------------------------------------------------
    */

    public function updateBookingStatus(
        int $bookingId,
        string $status
    ): bool {

        $allowedStatuses = [
            "Pending",
            "Paid",
            "Refunded"
        ];


        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $sql = "
            UPDATE bookings

            SET booking_status = ?

            WHERE booking_id = ?
        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "si",
            $status,
            $bookingId
        );


        return $stmt->execute();
    }



    /*
    |--------------------------------------------------------------------------
    | GET BOOKING BY REFERENCE
    |--------------------------------------------------------------------------
    */

    public function getBookingByReference(
        string $reference
    ): ?array {

        $sql = "
            SELECT
                *
            FROM bookings

            WHERE booking_reference = ?
        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "s",
            $reference
        );


        $stmt->execute();


        $result = $stmt->get_result();


        return $result->fetch_assoc() ?: null;
    }
}
