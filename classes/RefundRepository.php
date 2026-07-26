<?php

class RefundRepository
{
    private mysqli $conn;


    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }



    /*
    |--------------------------------------------------------------------------
    | GET BOOKING REFUND DETAILS
    |--------------------------------------------------------------------------
    */

    public function getBookingRefundDetails(
        int $bookingId
    ): ?array {


        $sql = "

            SELECT

                b.booking_id,

                b.booking_status,

                b.user_id,

                tp.price,

                COUNT(bs.seat_id) AS seat_count,

                p.payment_status

            FROM bookings b


            INNER JOIN show_schedules ss
                ON b.schedule_id = ss.schedule_id


            INNER JOIN movies m
                ON ss.movie_id = m.movie_id


            INNER JOIN ticket_prices tp
                ON m.movie_id = tp.movie_id


            INNER JOIN booking_seats bs
                ON b.booking_id = bs.booking_id

            INNER JOIN payments p
                ON p.booking_id = bs.booking_id


            WHERE b.booking_id = ?


            GROUP BY
                b.booking_id

        ";


        $stmt = $this->conn->prepare($sql);


        $stmt->bind_param(
            "i",
            $bookingId
        );


        $stmt->execute();


        $result =
            $stmt->get_result();


        return $result->fetch_assoc() ?: null;
    }





    /*
    |--------------------------------------------------------------------------
    | CALCULATE REFUND AMOUNT
    |--------------------------------------------------------------------------
    */

    public function calculateRefund(
        array $booking
    ): array {


        $originalAmount =

            $booking["price"]
            *
            $booking["seat_count"];



        $processingFee = 25;



        $refundAmount =

            $originalAmount
            -
            $processingFee;



        if ($refundAmount < 0) {

            $refundAmount = 0;
        }



        return [

            "original_amount" =>
            $originalAmount,


            "processing_fee" =>
            $processingFee,


            "refund_amount" =>
            $refundAmount

        ];
    }





    /*
    |--------------------------------------------------------------------------
    | UPDATE BOOKING STATUS
    |--------------------------------------------------------------------------
    */
    public function refundBooking(
        int $bookingId,
        int $user_id,
        string $reason
    ): bool {

        $sql = "
        INSERT INTO refund_history (booking_id, user_id, reason)
        VALUES (?, ?, ?)
    ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iis",
            $bookingId,
            $user_id,
            $reason
        );

        return $stmt->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK IF BOOKING CAN BE REFUNDED
    |--------------------------------------------------------------------------
    */

    public function canRefund(
        array $booking
    ): bool {


        return $booking["payment_status"] === "Paid";
    }
}
