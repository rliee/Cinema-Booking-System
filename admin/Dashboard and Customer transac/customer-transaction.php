<?php
include("customer-transaction-php/connection.php");
$sql = "
SELECT
    booking_transactions.transaction_code,
    customers.customer_name,
    movies.movie_title,
    booking_transactions.total_amount,
    booking_transactions.booking_date,
    booking_transactions.booking_status,
    booking_transactions.seats,
    booking_transactions.total_tickets
FROM booking_transactions
INNER JOIN customers
    ON booking_transactions.customer_id = customers.customer_id
INNER JOIN movies
    ON booking_transactions.movie_id = movies.movie_id
ORDER BY booking_transactions.booking_date DESC
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
            <button type="button" class="tab-btn active" data-tab="completed">Completed</button>
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

                <?php while($row = $result->fetch_assoc()) { ?>

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
                        <span
                            class="status-badge <?= strtolower($row['booking_status']) == 'completed' ? 'paid' : 'pending'; ?>">
                            <?= htmlspecialchars($row['booking_status']); ?>
                        </span>
                    </td>

                    <td>
                        <button onclick="details(
                            '<?= htmlspecialchars($row['transaction_code']); ?>',
                            '<?= htmlspecialchars($row['customer_name']); ?>',
                            '<?= htmlspecialchars($row['movie_title']); ?>',
                            '₱<?= number_format($row['total_amount'],2); ?>',
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