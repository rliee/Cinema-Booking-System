<?php

session_start();

require_once __DIR__ . "/auth/session.php";
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/classes/TransactionRepository.php";


if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["id"];

$transactionRepository = new TransactionRepository($conn);

// Fetch Transactions
$activeTransactions = $transactionRepository->getActiveTransactions($userId);
$refundedTransactions = $transactionRepository->getUserRefundedTransactions($userId);

function getMoviePoster($poster)
{
    if (!empty($poster)) {
        return $poster;
    }

    return "assets/images/poster/default.jpg";
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Royale Transactions</title>

    <link
        rel="stylesheet"
        href="./libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <link
        rel="stylesheet"
        href="./libraries/fontawesome/css/all.min.css">

    <link
        rel="stylesheet"
        href="./css/index.css">

    <link
        rel="stylesheet"
        href="./css/transaction.css">

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="logo\Logo.png" alt="Cinema Royale Logo" class="navbar-logo me-2" style="height: 2.5rem; width: auto;" />
                <div>
                    <span class="fs-2 p-0 m-0">Cinema Royale</span>
                    <div class="navbar-brand-subtitle ms-1" style="font-size: 0.75rem">PREMIUM EXPERIENCE</div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav mx-auto">
                    <div class="d-flex w-100 justify-content-center text-center">
                        <li class="nav-item"><a class="nav-link" href="#hero">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#now-showing">Now Showing</a></li>
                        <li class="nav-item"><a class="nav-link" href="#coming-soon">Coming Soon</a></li>
                    </div>
                    <div class="d-flex w-100 justify-content-center tex-center">
                        <li class="nav-item"><a class="nav-link" href="#promotions">Promotions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#experience">About</a></li>

                        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    </div>
                </ul>
                <div class="auth-buttons ms-auto d-flex flex-lg-row align-items-center justify-content-center my-2">

                    <?php if (isLoggedIn()): ?>

                        <?php
                        $firstName = explode(" ", $_SESSION["fullname"])[0];
                        ?>

                        <span class="text-white me-3">
                            Welcome,
                            <strong class="welcome-name"><?= htmlspecialchars($firstName) ?></strong>!
                        </span>

                        <a
                            href="booking.php"
                            class="auth-btn login-btn me-2">
                            My Bookings
                        </a>

                        <button
                            type="button"
                            class="auth-btn register-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#logoutModal">
                            Logout
                        </button>

                    <?php else: ?>

                        <button
                            class="auth-btn login-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#loginModal">
                            Login
                        </button>

                        <button
                            class="auth-btn register-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#registerModal">
                            Register
                        </button>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5 pt-5">
        <div
            class="page-header d-flex justify-content-between align-items-center">
            <h2>
                My Purchases
            </h2>

            <ul
                class="nav nav-pills"
                role="tablist">

                <li class="nav-item">
                    <button
                        class="nav-link active"
                        data-bs-toggle="pill"
                        data-bs-target="#active">
                        Active Transactions
                    </button>
                </li>

                <li class="nav-item">
                    <button
                        class="nav-link"
                        data-bs-toggle="pill"
                        data-bs-target="#history">
                        Refund History
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <!-- Active Transactions -->
            <div
                class="tab-pane fade show active"
                id="active">

                <?php if (count($activeTransactions) > 0): ?>

                    <div class="bookings-grid">
                        <?php foreach ($activeTransactions as $transaction): ?>

                            <div class="booking-card flex-row align-items-center">

                                <img
                                    src="<?php echo getMoviePoster($transaction['poster_url'] ?? ''); ?>"
                                    alt="Poster"
                                    class="booking-poster">

                                <div class="booking-details-content">

                                    <div>
                                        <!-- Movie Title -->
                                        <div class="movie-title-text">
                                            <?php echo htmlspecialchars($transaction["movie_title"]); ?>
                                        </div>

                                        <div class="booking-meta-grid">

                                            <!-- Hall -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-solid fa-ticket"></i>
                                                    Hall
                                                </div>

                                                <div class="info-value">
                                                    <?php echo htmlspecialchars($transaction["cinema_hall"]); ?>
                                                </div>
                                            </div>

                                            <!-- Date -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-regular fa-calendar-days"></i>
                                                    Date
                                                </div>

                                                <div class="info-value">
                                                    <?php echo htmlspecialchars($transaction["show_date"]); ?>
                                                </div>
                                            </div>

                                            <!-- Time -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-regular fa-clock"></i>
                                                    Time
                                                </div>

                                                <div class="info-value text-warning">
                                                    <?php echo htmlspecialchars($transaction["show_time"]); ?>
                                                </div>
                                            </div>

                                            <!-- Seats -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-solid fa-chair"></i>
                                                    Seats
                                                </div>

                                                <div class="info-value">

                                                    <?php foreach ($transaction["seats"] as $seat): ?>

                                                        <div class="transaction-seat-item mb-1">

                                                            <strong>
                                                                <?= htmlspecialchars($seat["seat_label"]); ?>
                                                            </strong>

                                                            <?php if (!empty($seat["discount_name"])): ?>

                                                                <span class="text-warning">
                                                                    (<?= htmlspecialchars($seat["discount_name"]); ?>)
                                                                </span>

                                                            <?php else: ?>

                                                                <span class="text-muted">
                                                                    (Regular)
                                                                </span>

                                                            <?php endif; ?>


                                                            <span class="text-success">
                                                                ₱<?= number_format(
                                                                        $seat["price"],
                                                                        2
                                                                    ); ?>
                                                            </span>

                                                        </div>

                                                    <?php endforeach; ?>

                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-solid fa-money-bill"></i>
                                                    Price
                                                </div>

                                                <div class="info-value text-success">
                                                    ₱<?php echo number_format(
                                                            $transaction["total_price"],
                                                            2
                                                        ); ?>
                                                </div>
                                            </div>

                                            <!-- Ticket Number -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-solid fa-barcode"></i>
                                                    Ticket No.
                                                </div>

                                                <div class="info-value">
                                                    <?php echo htmlspecialchars(
                                                        $transaction["ticket_number"]
                                                    ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="booking-card-actions">

                                        <?php

                                        $status = $transaction["status"] ?: "Pending";

                                        $statusClass = "";
                                        $statusIcon = "";

                                        switch ($status) {

                                            case "Pending":

                                                $statusClass = "pending";
                                                $statusIcon = "fa-clock";

                                                break;


                                            case "Paid":

                                                $statusClass = "paid";
                                                $statusIcon = "fa-circle-check";

                                                break;


                                            case "Refunded":

                                                $statusClass = "refunded";
                                                $statusIcon = "fa-rotate-left";

                                                break;


                                            case "Cancelled":

                                                $statusClass = "cancelled";
                                                $statusIcon = "fa-ban";

                                                break;
                                        }

                                        ?>


                                        <span class="badge-status <?= $statusClass; ?>">

                                            <i class="fa-solid <?= $statusIcon; ?>"></i>

                                            <?= htmlspecialchars($status); ?>

                                        </span>


                                        <?php if ($status === "Pending"): ?>

                                            <small class="text-warning">

                                                <i class="fa-solid fa-clock"></i>

                                                Waiting for payment confirmation

                                            </small>

                                        <?php endif; ?>


                                        <?php if ($status === "Paid"): ?>

                                            <button
                                                class="btn-refund"
                                                data-bs-toggle="modal"
                                                data-bs-target="#refundModal"
                                                data-bookingid="<?= $transaction["booking_id"]; ?>"
                                                data-movietitle="<?= htmlspecialchars(
                                                                        $transaction["movie_title"]
                                                                    ); ?>">

                                                <i class="fa-solid fa-rotate-left me-1"></i>

                                                Refund

                                            </button>

                                        <?php endif; ?>


                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="text-center py-5">
                        <h4 class="text-muted">
                            No active transactions found.
                        </h4>
                    </div>

                <?php endif; ?>

            </div>

            <!-- Refund History -->
            <div
                class="tab-pane fade"
                id="history">

                <?php if (count($refundedTransactions) > 0): ?>

                    <div class="bookings-grid">

                        <?php foreach ($refundedTransactions as $transaction): ?>


                            <div
                                class="booking-card flex-row align-items-center"
                                style="opacity:0.85;">

                                <img
                                    src="<?php echo getMoviePoster($transaction['poster_url'] ?? ''); ?>"
                                    alt="Poster"
                                    class="booking-poster">

                                <div class="booking-details-content">

                                    <div>
                                        <div class="movie-title-text text-white">

                                            <?php echo htmlspecialchars(
                                                $transaction["movie_title"]
                                            ); ?>

                                        </div>

                                        <div class="booking-meta-grid">

                                            <!-- Hall -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-solid fa-ticket"></i>
                                                    Hall
                                                </div>

                                                <div class="info-value">

                                                    <?php echo htmlspecialchars(
                                                        $transaction["cinema_hall"]
                                                    ); ?>

                                                </div>
                                            </div>

                                            <!-- Date -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-regular fa-calendar-days"></i>
                                                    Date
                                                </div>

                                                <div class="info-value">

                                                    <?php echo htmlspecialchars(
                                                        $transaction["show_date"]
                                                    ); ?>

                                                </div>
                                            </div>

                                            <!-- Time -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-regular fa-clock"></i>
                                                    Time
                                                </div>

                                                <div class="info-value">

                                                    <?php echo htmlspecialchars(
                                                        $transaction["show_time"]
                                                    ); ?>

                                                </div>
                                            </div>

                                            <!-- Seats -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-solid fa-chair"></i>
                                                    Seats
                                                </div>

                                                <div class="info-value">

                                                    <?php foreach ($transaction["seats"] as $seat): ?>

                                                        <div class="transaction-seat-item mb-1">

                                                            <strong>
                                                                <?= htmlspecialchars($seat["seat_label"]); ?>
                                                            </strong>

                                                            <?php if (!empty($seat["discount_name"])): ?>

                                                                <span class="text-warning">
                                                                    (<?= htmlspecialchars($seat["discount_name"]); ?>)
                                                                </span>

                                                            <?php else: ?>

                                                                <span class="text-muted">
                                                                    (Regular)
                                                                </span>

                                                            <?php endif; ?>

                                                            <span class="text-muted">
                                                                ₱<?= number_format(
                                                                        $seat["price"],
                                                                        2
                                                                    ); ?>
                                                            </span>

                                                        </div>

                                                    <?php endforeach; ?>

                                                </div>
                                            </div>

                                            <!-- Refund Amount -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-solid fa-money-bill"></i>
                                                    Refunded
                                                </div>

                                                <div class="info-value text-muted">

                                                    ₱<?php echo number_format(
                                                            $transaction["total_price"],
                                                            2
                                                        ); ?>
                                                </div>
                                            </div>

                                            <!-- Ticket Number -->
                                            <div>
                                                <div class="info-label">
                                                    <i class="fa-solid fa-barcode"></i>
                                                    Ticket No.
                                                </div>

                                                <div class="info-value">

                                                    <?php echo htmlspecialchars(
                                                        $transaction["ticket_number"]
                                                    ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="booking-card-actions">
                                        <span class="badge-status refunded">
                                            Refunded
                                        </span>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="text-center py-5">
                        <h4 class="text-muted">
                            No refund history found.
                        </h4>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div
        class="modal fade"
        id="refundModal"
        tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-warning">
                        Request Ticket Refund
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
                </div>

                <form
                    action="api/transactions/refund.php"
                    method="POST"
                    id="refundForm">

                    <div class="modal-body">
                        <input
                            type="hidden"
                            name="booking_id"
                            id="modal-booking-id">

                        <p>
                            Are you sure you want to refund your ticket for
                            <strong
                                id="modal-movie-title"
                                class="text-warning"></strong>
                            ?
                        </p>

                        <div class="mb-3">
                            <label
                                class="form-label">
                                Reason for refund
                            </label>

                            <select
                                class="form-select bg-dark"
                                name="refund_reason"
                                required>

                                <option
                                    value=""
                                    disabled
                                    selected>
                                    -- Select Reason --
                                </option>

                                <option value="Change of plans">
                                    Change of plans / Schedule conflict
                                </option>

                                <option value="Accidental booking">
                                    Accidental booking
                                </option>

                                <option value="Wrong schedule">
                                    Booked wrong date/time
                                </option>

                                <option value="Emergency">
                                    Personal emergency
                                </option>

                                <option value="Others">
                                    Others
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer border-secondary">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-danger"
                            id="confirmRefund">
                            Confirm Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {



            /*
                Refund Modal Handler
                --------------------
                Pass booking information
                into the modal
            */


            const refundModal =
                document.getElementById("refundModal");



            if (refundModal) {


                refundModal.addEventListener(
                    "show.bs.modal",
                    function(event) {



                        const button =
                            event.relatedTarget;



                        const bookingId =
                            button.getAttribute(
                                "data-bookingid"
                            );



                        const movieTitle =
                            button.getAttribute(
                                "data-movietitle"
                            );




                        document.getElementById(
                            "modal-booking-id"
                        ).value = bookingId;




                        document.getElementById(
                            "modal-movie-title"
                        ).textContent = movieTitle;




                    }
                );


            }

        });


        const refundElements = document.querySelectorAll("#refundForm")
        refundElements.forEach(async (form) => {
            form.addEventListener("submit", async (e) => {
                e.preventDefault()
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());


                const response = await fetch("./api/payments/refund.php", {
                    method: "POST",
                    body: JSON.stringify(data)
                })

                const result = await response.json()

                if (!response.ok || !result["success"]) {
                    return;
                }

                console.log(result)

            })
        })
    </script>


</body>

</html>