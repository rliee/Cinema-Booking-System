<?php
include("customer-transaction-php/connection.php");
$sql = "

SELECT

    b.booking_reference AS transaction_code,


    CONCAT(
        u.first_name,
        ' ',
        u.last_name
    ) AS customer_name,


    m.title AS movie_title,


    SUM(

        tp.price -
        (
            tp.price *
            IFNULL(
                d.discount_percentage,
                0
            ) / 100
        )

    ) AS total_amount,


    b.booking_date,


    p.payment_status AS booking_status,


    GROUP_CONCAT(
        s.seat_label
        ORDER BY s.seat_label
        SEPARATOR ', '
    ) AS seats,


    COUNT(
        bs.seat_id
    ) AS total_tickets



FROM bookings b



INNER JOIN users u

ON b.user_id = u.id



INNER JOIN show_schedules ss

ON b.schedule_id = ss.schedule_id



INNER JOIN movies m

ON ss.movie_id = m.movie_id



INNER JOIN booking_seats bs

ON b.booking_id = bs.booking_id



INNER JOIN payments p

ON p.booking_id = bs.booking_id



INNER JOIN seats s

ON bs.seat_id = s.seat_id



INNER JOIN ticket_prices tp

ON m.movie_id = tp.movie_id



LEFT JOIN discounts d

ON bs.discount_id = d.discount_id


GROUP BY

b.booking_id



ORDER BY

b.booking_date DESC

";


$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cinema Sales Transactions</title>

    <link href="customer-transaction.css" rel="stylesheet">
    <script src="customer-transaction.js" defer></script>
</head>

<body>
    <!-- Main Container -->
    <div class="container_custom_transac">
        <h1>Customers Transactions</h1>
        <!-- Tab navigation wrapper for filtering completed, pending, and weekly revenue views -->
        <div class="tab-nav" role="tablist" aria-label="Transaction tabs">
            <button type="button" class="tab-btn active" data-tab="paid">Completed</button>
            <button type="button" class="tab-btn" data-tab="pending">Pending</button>
            <button type="button" class="tab-btn" data-tab="weekly">Weekly Revenue</button>
        </div>

        <!-- Controls for search, date filtering, and booking status selection -->
        <div class="controls" id="tableControls">
            <input type="text" id="search" placeholder="Search Transaction...">

            <input type="date" id="dateFilter">

            <select id="statusFilter">
                <option value="">All Statuses</option>
                <option value="Completed">Completed</option>
                <option value="Pending">Pending</option>
            </select>
        </div>
        <!-- Weekly Revenue Panel -->
        <div class="weekly-panel_custom_transac" id="weeklyPanel" style="display:none;">
            <div class="weekly-heading">
                <div>
                    <h2>Weekly Movie Revenue</h2>
                    <p class="weekly-total">Total Monday – Sunday: <strong id="weeklyTotal">₱0</strong></p>
                </div>
                <span class="weekly-caption">Bar chart view</span>
            </div>
            <div class="weekly-chart" id="weeklyDays"></div>
        </div>
        <!-- Transaction Table -->
        <table id="transactionTable">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Customer</th>
                    <th>Movie</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Booking Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($row = $result->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($row['transaction_code']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['customer_name']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['movie_title']); ?>
                        </td>

                        <td data-amount="<?= (float) $row['total_amount']; ?>">
                            ₱
                            <?= number_format($row['total_amount'], 2); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['booking_date']); ?>
                        </td>


                        <td>
                            <span class="status-badge <?= strtolower($row['booking_status'])  ?>">
                                <?= htmlspecialchars($row['booking_status']); ?>
                            </span>
                        </td>

                        <td>
                            <button onclick="details(
                            '<?= htmlspecialchars($row['transaction_code']); ?>',
                            '<?= htmlspecialchars($row['customer_name']); ?>',
                            '<?= htmlspecialchars($row['movie_title']); ?>',
                            '₱<?= number_format($row['total_amount'], 2); ?>',
                            '<?= htmlspecialchars($row['seats']); ?>',
                            '<?= htmlspecialchars($row['total_tickets']); ?> Tickets',
                            '<?= htmlspecialchars($row['booking_status']); ?>'
                            )">
                                View
                            </button>
                        </td>

                    </tr>

                <?php } ?>

            </tbody>
        </table>

    </div>
    <!-- Modal -->
    <div class="modal_custom_transac" id="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Booking Details</h2>
            <div class="info" id="bookingInfo"></div>
            <button id="completeBookingBtn"
                class="complete-btn"
                onclick="completeBooking()">
                Completed
            </button>
        </div>
    </div>
</body>

</html>