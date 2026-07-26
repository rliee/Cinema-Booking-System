<?php

require_once __DIR__ . "/auth/session.php";
require_once __DIR__ . "/classes/DiscountRepository.php";
require_once __DIR__ . "/classes/TicketPricingRepository.php";
require_once __DIR__ . "/includes/db.php";

$discountService = new DiscountRepository($conn);
$discounts = $discountService->getDiscounts();

$ticketPricingService = new TicketPricingRepository($conn);

$movieId = $_GET["movie_id"];
if (!$movieId) {
    header("Location: http://localhost/cinema-booking/");
}
$movie = $ticketPricingService->getTicketPriceByMovieId($movieId);
if (!$movie) {
    header("Location: http://localhost/cinema-booking/");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cinema Royale - Premium Movie Experience</title>

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
        href="./css/booking.css">

    <link
        rel="stylesheet"
        href="./css/booking-seats.css">
</head>

<body>

    <div class="modal fade" id="registerModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-transparent border-0 text-white">
                <div class="auth-card">
                    <button class="close-btn" type="button" data-bs-dismiss="modal" aria-label="Close">×</button>
                    <div class="brand-side">
                        <img src="logo/Logo w text_.png" alt="Logo" class="logo-box" />
                        <h2>CINEMA ROYALE</h2>
                    </div>
                    <div class="form-side">
                        <div class="form-panel">
                            <h1>Create Account</h1>

                            <form id="register-form" method="POST">
                                <div class="form-group">
                                    <label for="first_name">Firstname</label>
                                    <input
                                        id="register-firstname"
                                        name="first_name"
                                        type="text"
                                        placeholder="Enter your firstname"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input
                                        id="register-lastname"
                                        name="last_name"
                                        type="text"
                                        placeholder="Enter your last name"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input
                                        id="register-email"
                                        name="email"
                                        type="email"
                                        placeholder="Enter your email"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input
                                        id="register-password"
                                        name="password"
                                        type="password"
                                        placeholder="Create a password"
                                        required>
                                </div>

                                <div
                                    id="register-error"
                                    class="text-danger register-error">
                                </div>

                                <button class="btn confirm-logout-btn" type="submit">Register</button>
                            </form>

                            <p class="alt-text text-start">
                                Already have an account?
                                <a
                                    href="#"
                                    class="alt-link"
                                    data-bs-toggle="modal"
                                    data-bs-target="#loginModal">
                                    Login
                                </a>
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-transparent border-0 text-white">
                <div class="auth-card">
                    <button class="close-btn" type="button" data-bs-dismiss="modal" aria-label="Close">×</button>
                    <div class="brand-side">
                        <img src="logo/Logo w text_.png" alt="Logo" class="logo-box" style="background-color: transparent; border:none;" />
                        <h2>CINEMA ROYALE</h2>
                    </div>
                    <div class="form-side">
                        <div class="form-panel">
                            <h1>Login</h1>
                            <p class="subtitle">Login to continue</p>

                            <form id="login-form" method="POST">
                                <div class="form-group">
                                    <label for="login-email">Email</label>
                                    <input
                                        id="login-email"
                                        name="email"
                                        type="email"
                                        autocomplete="off"
                                        placeholder="Enter your email"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="login-password">Password</label>
                                    <input
                                        id="login-password"
                                        name="password"
                                        type="password"
                                        placeholder="Enter your password"
                                        required>
                                </div>

                                <div class="actions">
                                    <label><input type="checkbox" /> Remember me</label>
                                    <a href="#">Forgot password?</a>
                                </div>

                                <div
                                    id="login-error"
                                    class="text-danger login-error">
                                </div>

                                <button
                                    class="btn confirm-logout-btn"
                                    type="submit">
                                    Login
                                </button>
                            </form>

                            <p class="alt-text text-start">
                                Don't have an account?
                                <a
                                    href="#"
                                    class="alt-link"
                                    data-bs-toggle="modal"
                                    data-bs-target="#registerModal">
                                    Create an account
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div
        class="modal fade logout-modal-wrapper"
        id="logoutModal"
        tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content logout-modal">
                <div class="modal-header">
                    <h5 class="modal-title">PWD
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Confirm Logout
                    </h5>

                    <button
                        type="button"
                        class="btn-close logout-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">
                    <div class="logout-message">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <p>
                            Are you sure you want to log out?
                        </p>
                    </div>
                </div>

                <div class="modal-footer logout-actions">
                    <button
                        type="button"
                        class="btn stay-btn"
                        data-bs-dismiss="modal">
                        Stay Logged In
                    </button>

                    <a
                        href="api/auth/logout.php"
                        class="btn confirm-logout-btn">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="discountVerificationModal"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content logout-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-id-card"></i>
                        Verify Discount
                    </h5>

                    <button
                        type="button"
                        class="btn-close logout-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        Please enter your ID number to verify this discount.
                    </p>

                    <input
                        type="text"
                        class="form-control"
                        id="discountIdInput"
                        placeholder="Enter ID Number">
                </div>

                <div class="modal-footer">
                    <button
                        class="btn stay-btn"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        class="btn confirm-logout-btn"
                        id="confirmDiscountBtn">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

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

    <main class="booking-page">

        <!-- MOVIE HERO SECTION -->
        <section class="hero-section">
            <div class="hero-overlay"></div>

            <div class="hero-container">
                <img
                    id="moviePoster"
                    src=""
                    alt="Movie Poster"
                    class="movie-poster">

                <div class="movie-details">
                    <span class="badge">
                        <span class="dot"></span>
                        NOW SHOWING
                    </span>

                    <h2 id="movieTitle">
                        Movie Title
                    </h2>

                    <div class="meta-tags">
                        <span>
                            <i class="fa-solid fa-clock"></i>
                            <span id="movieDuration">
                                2h 00m
                            </span>
                        </span>
                        <span class="tag" id="movieRating">
                            PG
                        </span>
                        <span id="movieGenre">
                            Action
                        </span>
                    </div>

                    <p
                        class="hero-synopsis"
                        id="movieSynopsis">
                        Movie synopsis will appear here.
                    </p>
                </div>
            </div>
        </section>

        <!-- BOOKING FLOW -->
        <section class="booking-flow container">
            <h2 class="booking-page-title">
                Book Tickets
            </h2>

            <h3 class="section-title">
                Select Showtime
            </h3>

            <p class="section-subtitle">
                Choose your preferred schedule.
            </p>

            <div class="booking-grid">

                <!-- DATE -->
                <div class="booking-col">

                    <h4 class="step-title">
                        <span class="step-num">1</span>
                        Select Date
                    </h4>

                    <div
                        class="selectable-list"
                        id="date-list">
                    </div>

                </div>


                <!-- HALL -->
                <div
                    class="booking-col d-none"
                    id="hall-section">

                    <h4 class="step-title">
                        <span class="step-num">2</span>
                        Select Hall
                    </h4>


                    <div
                        class="selectable-list"
                        id="hall-list">
                    </div>

                </div>


                <!-- TIME -->
                <div
                    class="booking-col d-none"
                    id="time-section">

                    <h4 class="step-title">
                        <span class="step-num">3</span>
                        Select Time
                    </h4>


                    <div
                        class="selectable-list"
                        id="time-list">
                    </div>

                </div>

            </div>

            <!-- TICKET TYPES -->
            <div class="booking-type-section d-none" id="ticketSection">
                <h4 class="step-title">
                    <span class="step-num">4</span>
                    Select Tickets
                </h4>

                <div class="type-grid">

                    <?php
                    $price = $movie["price"] ?? 0;
                    $formattedPHP = '₱' . number_format($price, 2, '.', ',');


                    if ($discounts) {
                        foreach ($discounts as $item) {
                            $percentage = $item["discount_percentage"];

                            $priceMessage =  $percentage . "%";
                            if ($item["discount_name"] == "Regular") {
                                $priceMessage = $formattedPHP;
                            }

                            echo '<div class="type-card">
                        <div class="type-card-header">
                            <span>
                                ' . $item["discount_name"] . '
                            </span>
                            <span id="regular-price">
                                ' . $priceMessage . ' 
                            </span>
                        </div>

                        <input
                            type="number"
                            min="0"
                            value="0"
                            data-discount="' . $item["discount_percentage"] . '"
                            data-name="' . $item["discount_name"] . '"
                            data-id="' . $item["discount_id"] . '"
                            id="seat-discount">

                    </div>';
                        }
                    }

                    ?>
                    <!-- <div class="type-card">
                        <div class="type-card-header">
                            <span>
                                Regular
                            </span>
                            <span id="regular-price">
                                ₱0
                            </span>
                        </div>

                        <input
                            type="number"
                            min="0"
                            value="0"
                            id="regular-count">

                    </div>

                    <div class="type-card">
                        <div class="type-card-header">
                            <span>
                                Student
                            </span>
                            <span
                                id="student-discount"
                                class="discount-badge">
                                0%
                            </span>
                        </div>

                        <input
                            type="number"
                            min="0"
                            value="0"
                            id="student-count">

                    </div>

                    <div class="type-card">
                        <div class="type-card-header">
                            <span>
                                Senior Citizen
                            </span>
                            <span
                                id="senior-discount"
                                class="discount-badge">
                                0%
                            </span>
                        </div>

                        <input
                            type="number"
                            min="0"
                            value="0"
                            id="senior-count">
                    </div>

                    <div class="type-card">
                        <div class="type-card-header">
                            <span>
                                PWD
                            </span>
                            <span
                                id="pwd-discount"
                                class="discount-badge">
                                0%
                            </span>
                        </div>

                        <input
                            type="number"
                            min="0"
                            value="0"
                            id="pwd-count">
                    </div> -->
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="summary-checkout-bar">
                <div class="summary-details">
                    <div class="summary-item">
                        <span>
                            Movie
                        </span>
                        <strong id="summary-movie">
                            -
                        </strong>
                    </div>

                    <div class="summary-item">
                        <span>
                            Date
                        </span>
                        <strong id="summary-date">
                            -
                        </strong>
                    </div>

                    <div class="summary-item">
                        <span>
                            Hall
                        </span>
                        <strong id="summary-hall">
                            -
                        </strong>
                    </div>

                    <div class="summary-item">
                        <span>
                            Time
                        </span>
                        <strong id="summary-time">
                            -
                        </strong>
                    </div>

                    <div class="summary-item">
                        <span>
                            Total
                        </span>
                        <strong
                            id="summary-price">
                            ₱0
                        </strong>
                    </div>
                </div>

                <button
                    class="book-ticket-btn d-none"
                    id="checkout-btn"
                    disabled>
                    <i class="fa-solid fa-ticket"></i>
                    Select Seats
                </button>
            </div>

            <!-- ==========================================================
                 SEAT SELECTION
                ========================================================== -->
            <section
                class="seat-selection-section d-none"
                id="seatSelectionSection">
                <div class="container">

                    <div class="booking-layout">

                        <!-- LEFT SIDE -->
                        <div class="seat-area">

                            <!-- Screen -->
                            <div class="screen">
                                SCREEN
                            </div>

                            <!-- Legend -->
                            <div class="seat-legend">

                                <div class="legend-item">
                                    <span class="legend-box available"></span>
                                    <span>Available</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-box selected"></span>
                                    <span>Selected</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-box occupied"></span>
                                    <span>Occupied</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-box unavailable"></span>
                                    <span>Unavailable</span>
                                </div>

                            </div>

                            <!-- Seat Layout -->
                            <div
                                class="seat-layout"
                                id="seatLayout">

                                <!-- Generated by booking.js -->

                            </div>
                        </div>

                        <!-- RIGHT SIDE -->
                        <aside class="booking-summary">

                            <h3>Booking Summary</h3>

                            <hr>
                            <p>
                                <strong>Movie</strong>
                                <span id="summaryMovie"></span>
                            </p>

                            <p>
                                <strong>Date</strong>
                                <span id="summaryDate">—</span>
                            </p>

                            <p>
                                <strong>Hall</strong>
                                <span id="summaryHall">—</span>
                            </p>

                            <p>
                                <strong>Time</strong>
                                <span id="summaryTime">—</span>
                            </p>

                            <hr>

                            <p>
                                <strong>Selected Seats</strong>
                                <span id="summarySeats">None</span>
                            </p>

                            <p>
                                <strong>Total Seats</strong>
                                <span id="summarySeatCount">0</span>
                            </p>

                            <div id="ticketBreakdown">

                            </div>

                            <p class="summary-total">
                                <strong>Total</strong>
                                <span id="summaryTotal">₱0.00</span>
                            </p>

                            <button
                                class="btn btn-warning w-100 mt-4"
                                id="continueBookingBtn"
                                disabled>
                                Continue
                            </button>
                        </aside>
                    </div>
                </div>
            </section>
        </section>
    </main>







    <footer id="contact">
        <div class="container">
            <div class="footer-section">
                <h5>🎬 Cinema Royale</h5>
                <p>
                    Experience movies the way they were meant to be seen. Premium sound, stunning visuals, and unmatched comfort — only at Cinema Royale.
                </p>
                <div class="footer-socials">
                    <a href="#" title="Facebook">f</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="Twitter">𝕏</a>
                    <a href="#" title="YouTube">▶</a>
                </div>
            </div>
            <div class="footer-section">
                <h5>NEWSLETTER</h5>
                <p>Get the latest movies, exclusive offers, and event invites straight to your inbox.</p>
                <div class="newsletter-input">
                    <input type="email" placeholder="Your email address" />
                    <button>→</button>
                </div>
            </div>
            <div class="footer-section">
                <h5>QUICK LINKS</h5>
                <div class="footer-links">
                    <a href="#now-showing">Now Showing</a>
                    <a href="#promotions">Promotions</a>
                    <a href="#experience">About Us</a>
                    <a href="#contact">Contact</a>
                </div>
            </div>
            <div class="footer-section">
                <h5>CONTACT</h5>
                <p><i class="fa-solid fa-location-dot"></i> 📍 Trece Martires City, Cavite 4109</p>
                <p>📞 +63 949 141 3401</p>
                <p>📧 <a href="mailto:hello@cinemaroyale.com" style="color: #ffc700; text-decoration: none">hello@cinemaroyale.com</a></p>
            </div>
            <div class="footer-section">
                <h5>TEAM</h5>
                <p>
                    Arliesienne Ansuas<br />Genesis Saliedo<br />James Arnold Dutosme<br />Josiah Joshua Torrefiel
                    <br />Kier Bryant Levita<br />Kylle Jonathan Padua<br />Ron Andrei Castro
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-watermark">CINEMA ROYALE</div>
            <p class="footer-copyright">© 2026 Cinema Royale. All rights reserved.</p>
        </div>
    </footer>

    <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/app.js"></script>
    <script src="js/index.js"></script>

    <script src="js/auth.js"></script>
    <script src="js/login.js"></script>
    <script src="js/register.js"></script>
    <script src="js/booking.js"></script>

</body>

</html>