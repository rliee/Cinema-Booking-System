<?php

class PaymentRepository
{
    private mysqli $conn;


    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }


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

    public function createPayment(int $bookingId)
    {
        $sql = "
        INSERT INTO payments
        (
            booking_id,
            payment_status
        )
        VALUES
        (
            ?,
            'Unpaid'
        )
    ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $bookingId
        );


        return $stmt->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Payment Details By Booking Reference
    |--------------------------------------------------------------------------
    | Used by payment.php?id=TKT-XXXXXXX
    |
    */
    public function getPaymentByReference(string $reference)
    {
        $sql = "
            SELECT
                b.booking_id,
                b.booking_reference,
                b.booking_status,

                m.title AS movie_title,

                s.show_date,
                s.start_time,

                h.hall_name,

                tp.price AS ticket_price



            FROM bookings b

            INNER JOIN show_schedules s
                ON b.schedule_id = s.schedule_id

            INNER JOIN movies m
                ON s.movie_id = m.movie_id

            INNER JOIN cinema_halls h
                ON s.hall_id = h.hall_id

            INNER JOIN ticket_prices tp
                ON tp.movie_id = m.movie_id

            WHERE b.booking_reference = ?

            GROUP BY b.booking_id
        ";


        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "s",
            $reference
        );

        $stmt->execute();


        $result = $stmt->get_result();


        if ($result->num_rows === 0) {
            return null;
        }


        $booking = $result->fetch_assoc();


        // Calculate amount dynamically
        $booking["amount"] = $this->calculateAmount(
            $booking["booking_id"],
            $booking["ticket_price"]
        );


        // Get payment status
        $booking["payment_status"] =
            $this->getPaymentStatus(
                $booking["booking_id"]
            );


        return $booking;
    }




    /*
        Calculate total amount

        Each seat can have different discount
    */
    private function calculateAmount(
        int $bookingId,
        float $ticketPrice
    ) {

        $sql = "
            SELECT
                d.discount_percentage

            FROM booking_seats bs

            LEFT JOIN discounts d
                ON bs.discount_id = d.discount_id

            WHERE bs.booking_id = ?
        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $bookingId
        );


        $stmt->execute();


        $result = $stmt->get_result();


        $total = 0;


        while ($row = $result->fetch_assoc()) {

            if ($row["discount_percentage"]) {

                $discounted =
                    $ticketPrice -
                    (
                        $ticketPrice *
                        $row["discount_percentage"]
                        / 100
                    );


                $total += $discounted;
            } else {
                $total += $ticketPrice;
            }
        }


        return $total;
    }

    private function getPaymentStatus(int $bookingId)
    {

        $sql = "
            SELECT payment_status

            FROM payments

            WHERE booking_id = ?

            LIMIT 1
        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $bookingId
        );


        $stmt->execute();


        $result = $stmt->get_result();


        if ($result->num_rows === 0) {
            return "Unpaid";
        }


        return $result->fetch_assoc()["payment_status"];
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Submitted Payment
    |--------------------------------------------------------------------------
    |
    | Unpaid -> Pending
    |
    */

    public function submitPayment(
        int $paymentId,
        string $paymentMethod
    ) {

        $sql = "

            UPDATE payments

            SET

                payment_status = 'Pending',
                payment_method = ?

            WHERE payment_id = ?

            AND payment_status = 'Unpaid'

        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "si",
            $paymentMethod,
            $paymentId
        );


        return $stmt->execute();
    }




    /*
    |--------------------------------------------------------------------------
    | Admin Update Payment Status
    |--------------------------------------------------------------------------
    |
    | Pending -> Paid / Failed
    |
    */

    public function updatePaymentStatus(
        int $paymentId,
        string $status
    ) {

        $allowedStatus = [
            "Paid",
            "Failed"
        ];


        if (!in_array($status, $allowedStatus)) {
            return false;
        }


        $sql = "

            UPDATE payments

            SET payment_status = ?

            WHERE payment_id = ?

            AND payment_status = 'Pending'

        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "si",
            $status,
            $paymentId
        );


        return $stmt->execute();
    }




    /*
    |--------------------------------------------------------------------------
    | Get Payment By ID
    |--------------------------------------------------------------------------
    | Useful for admin/payment management
    |
    */

    public function getPaymentById(
        int $paymentId
    ) {

        $sql = "

            SELECT *

            FROM payments

            WHERE payment_id = ?

            LIMIT 1

        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $paymentId
        );


        $stmt->execute();


        $result = $stmt->get_result();


        return $result->fetch_assoc();
    }





    /*
    |--------------------------------------------------------------------------
    | Get User Payment History
    |--------------------------------------------------------------------------
    | Used for customer transaction history
    |
    */

    public function getUserPayments(
        int $userId
    ) {

        $sql = "

            SELECT

                p.payment_id,

                p.amount,

                p.payment_status,

                p.payment_method,

                p.payment_date,


                b.booking_reference,


                m.title AS movie_title,


                s.show_date,

                s.start_time


            FROM payments p


            INNER JOIN bookings b
                ON p.booking_id = b.booking_id


            INNER JOIN schedules s
                ON b.schedule_id = s.schedule_id


            INNER JOIN movies m
                ON s.movie_id = m.movie_id


            WHERE b.user_id = ?


            ORDER BY p.payment_date DESC

        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $userId
        );


        $stmt->execute();


        $result = $stmt->get_result();


        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
