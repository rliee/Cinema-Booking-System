<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Royale - Booking</title>
    <link rel="stylesheet" href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="libraries/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/booking.css">
    <script>
        (function() {
            if (localStorage.getItem("loggedIn") !== "true") {
                window.location.href = "index.php";
            }
        })();
    </script>
</head>
<body>
    <!-- Discount Verification Modal -->
    <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: #1a1a1a; border: 1px solid rgba(255, 199, 0, 0.2); color: #fff;">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255, 199, 0, 0.1);">
                    <h5 class="modal-title gold-text" id="verificationModalLabel" style="color: #ffc700; font-weight: 700;">
                        <i class="fa-solid fa-id-card me-2"></i><span id="modalDiscountType">ID</span> Verification Required
                    </h5>
                    <button type="button" class="btn-close btn-close-white" id="cancelVerificationBtn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" style="font-size: 0.9rem;" id="modalInstructions">
                        Please upload a valid ID to claim this discount.
                    </p>
                    <form id="verificationForm">
                        <div class="mb-3">
                            <label for="idNumberInput" class="form-label" style="font-weight: 600; color: #ddd;">ID / Card Number</label>
                            <input type="text" class="form-control" id="idNumberInput" placeholder="Enter ID number" required style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid #444;">
                        </div>
                        <div class="mb-3">
                            <label for="idImageInput" class="form-label" style="font-weight: 600; color: #ddd;">Upload Valid ID Image</label>
                            <input class="form-control" type="file" id="idImageInput" accept="image/*" required style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid #444;">
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255, 199, 0, 0.1);">
                    <button type="button" class="btn btn-outline-secondary" id="declineVerificationBtn" data-bs-dismiss="modal" style="border-radius: 20px;">Cancel</button>
                    <button type="button" class="btn btn-warning" id="saveVerificationBtn" style="background: #ffc700; font-weight: 700; border-radius: 20px;">Confirm Verification</button>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="logo/Logo.png" alt="Cinema Royale Logo" class="navbar-logo me-2" style="height: 2.5rem; width: auto;" />
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
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#now-showing">Now Showing</a></li>
                    </div>
                    <div class="d-flex w-100 justify-content-center text-center">
                        <li class="nav-item"><a class="nav-link" href="index.php#promotions">Promotions</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#experience">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
                    </div>
                </ul>
                <div class="auth-buttons ms-auto d-flex flex-lg-row justify-content-center my-2">
                    <button class="auth-btn login-btn" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                    <button class="auth-btn register-btn" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-container">
            <img id="moviePoster" src="" alt="Movie Poster" class="movie-poster">
            <div class="movie-details">
                <span class="badge"><span class="dot"></span> NOW SHOWING</span>
                <h2 class="movie-title" id="movieTitle">Loading...</h2>
                <div class="meta-tags">
                    <span><i class="fa-solid fa-star gold-text"></i> <span id="heroRating">0.0</span> <small>/10</small></span>
                    <span class="tag" id="heroAgeRating">--</span>
                    <span><i class="fa-regular fa-clock"></i> <span id="heroDuration">--</span></span>
                    <span id="heroGenreText">--</span>
                </div>
                <p class="hero-synopsis" id="heroSynopsis">Loading synopsis...</p>
            </div>
        </div>
    </section>

    <main class="content-container">
        <div class="left-column">
            <section class="details-section">
                <h3>Synopsis</h3>
                <p id="detailSynopsis">Loading synopsis...</p>
            </section>
            <section class="details-section">
                <h3>Cast & Crew</h3>
                <div class="cast-grid" id="castGrid"></div>
                <div class="director-row">
                    <i class="fa-solid fa-clapperboard text-muted"></i> <span>Director: <strong id="directorName">--</strong></span>
                </div>
            </section>
            <section class="details-section">
                <h3>Official Trailer</h3>
                <video id="trailerLink" class="video-container" controls style="width: 100%;">
                    <source src="" type="video/mp4">
                </video>
            </section>
        </div>
        <aside class="right-column">
            <div class="facts-card">
                <h3>Quick Facts</h3>
                <div class="fact-item">
                    <i class="fa-regular fa-calendar-days icon-box"></i>
                    <div>
                        <p class="fact-label">Release Date</p>
                        <p class="fact-value" id="factReleaseDate">--</p>
                    </div>
                </div>
                <div class="fact-item">
                    <i class="fa-regular fa-clock icon-box"></i>
                    <div>
                        <p class="fact-label">Duration</p>
                        <p class="fact-value" id="factDuration">--</p>
                    </div>
                </div>
                <div class="fact-item">
                    <i class="fa-solid fa-shield-halved icon-box"></i>
                    <div>
                        <p class="fact-label">Rating</p>
                        <p class="fact-value" id="factRating">--</p>
                    </div>
                </div>
                <div class="fact-item">
                    <i class="fa-solid fa-film icon-box"></i>
                    <div>
                        <p class="fact-label">Genre</p>
                        <p class="fact-value" id="factGenre">--</p>
                    </div>
                </div>
                <div class="fact-item">
                    <i class="fa-solid fa-video icon-box"></i>
                    <div>
                        <p class="fact-label">Director</p>
                        <p class="fact-value" id="factDirector">--</p>
                    </div>
                </div>
                <div class="price-range-section">
                    <p class="fact-label">Ticket Price Range</p>
                    <div class="price-badges">
                        <div class="price-tier">
                            <span class="tier-name">Standard</span>
                            <span class="tier-cost">₱350</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <section class="booking-flow content-container">
        <h2 class="booking-page-title" id="booking-movie-title">Book tickets</h2>
        <h3 class="section-title">Select Showtime</h3>
        <p class="section-subtitle">Choose your preferred date, cinema hall, and screening time</p>
        <div class="booking-grid">
            <div class="booking-col">
                <h4 class="step-title"><span class="step-num">1</span> Select Date</h4>
                <div class="selectable-list" id="date-list"></div>
            </div>
            <div class="booking-col">
                <h4 class="step-title"><span class="step-num">2</span> Select Hall</h4>
                <div class="selectable-list disabled" id="hall-list">
                    <div class="hall-placeholder">Select a date first</div>
                    <div class="select-item item-detailed" data-value="Cinema Hall 1" data-price="350">
                        <div>
                            <div class="item-header-row">
                                <span class="hall-title">Cinema Hall 1</span>
                                <span class="mini-badge standard">STANDARD</span>
                            </div>
                        </div>
                    </div>
                    <div class="select-item item-detailed" data-value="Cinema Hall 2" data-price="350">
                        <div>
                            <div class="item-header-row">
                                <span class="hall-title">Cinema Hall 2</span>
                                <span class="mini-badge standard">STANDARD</span>
                            </div>
                        </div>
                    </div>
                    <div class="select-item item-detailed" data-value="Cinema Hall 3" data-price="350">
                        <div>
                            <div class="item-header-row">
                                <span class="hall-title">Cinema Hall 3</span>
                                <span class="mini-badge standard">STANDARD</span>
                            </div>
                        </div>
                    </div>
                    <div class="select-item item-detailed" data-value="Cinema Hall 4" data-price="350">
                        <div>
                            <div class="item-header-row">
                                <span class="hall-title">Cinema Hall 4</span>
                                <span class="mini-badge standard">STANDARD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="booking-col">
                <h4 class="step-title"><span class="step-num">3</span> Select Time</h4>
                <div class="selectable-list disabled" id="time-list">
                    <div class="time-placeholder">Select a hall first</div>
                    <div class="select-item item-horizontal" data-time="11:00 AM" data-price="350">
                        <div>
                            <span class="time-text">11:00 AM</span>
                        </div>
                        <span class="price-tag">₱350</span>
                    </div>
                    <div class="select-item item-horizontal" data-time="3:00 PM" data-price="350">
                        <div>
                            <span class="time-text">3:00 PM</span>
                        </div>
                        <span class="price-tag">₱350</span>
                    </div>
                    <div class="select-item item-horizontal" data-time="6:30 PM" data-price="350">
                        <div>
                            <span class="time-text">6:30 PM</span>
                        </div>
                        <span class="price-tag">₱350</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="booking-type-section">
            <h4 class="step-title"><span class="step-num">4</span> Select Type</h4>
            <p class="step-subtitle">Choose how many Regular, Senior Citizen, Student and PWD tickets you want.</p>
            <div class="type-grid">
                <label class="type-card" data-type="Regular">
                    <div class="type-card-header">
                        <span>Regular</span>
                        <span class="badge type-badge price">₱350</span>
                    </div>
                    <p class="type-description">Fixed price ticket</p>
                    <input id="countRegular" class="ticket-count type-input" type="number" min="0" value="0">
                </label>
                <label class="type-card" data-type="Senior Citizen">
                    <div class="type-card-header">
                        <span>Senior Citizen</span>
                        <span class="badge type-badge discount">20% off</span>
                    </div>
                    <p class="type-description">Discounted senior price</p>
                    <input id="countSenior" class="ticket-count type-input" type="number" min="0" value="0">
                </label>
                <label class="type-card" data-type="Student">
                    <div class="type-card-header">
                        <span>Student</span>
                        <span class="badge type-badge discount">15% off</span>
                    </div>
                    <p class="type-description">Discounted student price</p>
                    <input id="countStudent" class="ticket-count type-input" type="number" min="0" value="0">
                </label>
                <label class="type-card" data-type="PWD">
                    <div class="type-card-header">
                        <span>PWD</span>
                        <span class="badge type-badge discount">20% off</span>
                    </div>
                    <p class="type-description">Discounted PWD ticket</p>
                    <input id="countPWD" class="ticket-count type-input" type="number" min="0" value="0">
                </label>
            </div>
        </div>

        <div class="summary-checkout-bar">
            <div class="summary-details">
                <div class="summary-item">
                    <span class="summary-lbl">Movie</span>
                    <span class="summary-val" id="summary-movie">--</span>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Date</span>
                    <span class="summary-val" id="summary-date">Choose a date</span>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Hall</span>
                    <span class="summary-val" id="summary-hall">Choose a hall after selecting a date</span>
                </div>
                <div class="summary-item summary-time-with-type">
                    <span class="summary-lbl">Time</span>
                    <div style="display:flex;align-items:center;gap:12px">
                        <span class="summary-val" id="summary-time">Choose a time after selecting a hall</span>
                    </div>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Price</span>
                    <span class="summary-val gold-text" id="summary-price">₱0</span>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Tickets (R / S / St / P)</span>
                    <span class="summary-val"><span id="counts-display">0 / 0 / 0 / 0</span></span>
                </div>
                <div class="summary-item">
                    <span class="summary-lbl">Total Tickets</span>
                    <span class="summary-val"><span id="total-tickets-display">0</span></span>
                </div>
            </div>
            <button class="book-ticket-btn" id="checkout-btn">
                <i class="fa-solid fa-ticket"></i> Select Seats
            </button>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <div class="footer-section">
                <h5>🎬 Cinema Royale</h5>
                <p>Experience movies the way they were meant to be seen. Premium sound, stunning visuals, and unmatched comfort — only at Cinema Royale.</p>
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
                    <input type="email" placeholder="Your email address">
                    <button>→</button>
                </div>
            </div>
            <div class="footer-section">
                <h5>QUICK LINKS</h5>
                <div class="footer-links">
                    <a href="index.php#now-showing">Now Showing</a>
                    <a href="index.php#promotions">Promotions</a>
                    <a href="index.php#experience">About Us</a>
                    <a href="index.php#contact">Contact</a>
                </div>
            </div>
            <div class="footer-section">
                <h5>CONTACT</h5>
                <p><i class="fa-solid fa-location-dot"></i> Trece Martires City, Cavite 4109</p>
                <p>📞 +63 949 141 3401</p>
                <p>📧 <a href="mailto:hello@cinemaroyale.com" style="color:#ffc700;text-decoration:none;">hello@cinemaroyale.com</a></p>
            </div>
            <div class="footer-section">
                <h5>TEAM</h5>
                <p>Arliesienne Ansuas<br>Ron Andrei Castro<br>James Arnold Dutosme<br>Kier Bryant Levita<br>Kylle Jonathan Padua<br>Genesis Saliedo<br>Josiah Joshua Torrefiel</p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-watermark">CINEMA ROYALE</div>
            <p class="footer-copyright">© 2026 Cinema Royale. All rights reserved.</p>
        </div>
    </footer>

    <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Session Handler
        const loggedIn = localStorage.getItem("loggedIn");
        const userName = localStorage.getItem("userName") || "User";

        if (loggedIn === "true") {
            const authButtons = document.querySelector(".auth-buttons");
            if (authButtons) {
                authButtons.innerHTML = `
                    <span class="welcome-text" style="color: #fff; margin-right: 15px; font-weight: 600;">
                        Welcome, ${userName}!
                    </span>
                    <a href="#" id="logout-btn" class="auth-btn login-btn" style="text-decoration: none;">Logout</a>
                `;

                document.getElementById("logout-btn").addEventListener("click", function (e) {
                    e.preventDefault();
                    localStorage.removeItem("loggedIn");
                    localStorage.removeItem("userName");
                    localStorage.removeItem("userEmail");
                    window.location.href = "index.php";
                });
            }
        }

        // Navbar collapse close on link click
        document.querySelectorAll(".nav-link").forEach((link) => {
            link.addEventListener("click", () => {
                document.querySelector(".navbar-collapse")?.classList.remove("show");
            });
        });

        // Hide header on scroll down
        let lastScrollTop = 0;
        const header = document.querySelector('.navbar');
        window.addEventListener('scroll', function () {
            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            if (currentScroll > lastScrollTop && currentScroll > 50) {
                header.classList.add('hide-header');
            } else {
                header.classList.remove('hide-header');
            }
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        });
    });
    </script>

    <script>
        (function() {
            const params = new URLSearchParams(window.location.search);
            // Grab URL query param or fallback to pendingMovie / stored movie
            let selectedMovieTitle = params.get('movie') || localStorage.getItem('pendingMovie') || localStorage.getItem('movie');
            
            // Clear pendingMovie once processed
            localStorage.removeItem('pendingMovie');

            // Clear ticket counts when entering booking page to start fresh
            localStorage.removeItem('ticketCounts');
            localStorage.removeItem('selectedSeats');

            const bookingTitle = document.getElementById('booking-movie-title');
            const summaryMovie = document.getElementById('summary-movie');
            const summaryDate = document.getElementById('summary-date');
            const summaryHall = document.getElementById('summary-hall');
            const summaryTime = document.getElementById('summary-time');
            const summaryPrice = document.getElementById('summary-price');
            const checkoutBtn = document.getElementById('checkout-btn');
            const hallList = document.getElementById('hall-list');
            const timeList = document.getElementById('time-list');
            const moviePoster = document.getElementById('moviePoster');
            const movieTitleEl = document.getElementById('movieTitle');
            const heroRating = document.getElementById('heroRating');
            const heroAgeRating = document.getElementById('heroAgeRating');
            const heroDuration = document.getElementById('heroDuration');
            const heroGenreText = document.getElementById('heroGenreText');
            const heroSynopsis = document.getElementById('heroSynopsis');
            const detailSynopsis = document.getElementById('detailSynopsis');
            const castGrid = document.getElementById('castGrid');
            const directorNameEl = document.getElementById('directorName');
            const trailerLink = document.getElementById('trailerLink');
            const factReleaseDate = document.getElementById('factReleaseDate');
            const factDuration = document.getElementById('factDuration');
            const factRating = document.getElementById('factRating');
            const factGenre = document.getElementById('factGenre');
            const factDirector = document.getElementById('factDirector');
            const heroSection = document.querySelector('.hero-section');

            const movieDetails = {
                'Avengers: Infinity War': {
                    poster: 'assets/images/poster/image1.jpg',
                    bgImage: 'assets/images/poster/image1.jpg',
                    title: 'Avengers: Infinity War',
                    synopsis: 'The Avengers and their allies unite to stop the powerful Thanos from collecting all six Infinity Stones, which would give him the power to wipe out half of all life in the universe.',
                    rating: '9.2',
                    ageRating: 'PG-13',
                    duration: '2h 48m',
                    genre: 'Sci-Fi / Adventure',
                    releaseDate: 'April 27, 2018',
                    director: 'Anthony Russo & Joe Russo',
                    cast: [
                        { name: 'Robert Downey Jr.', image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150' },
                        { name: 'Chris Evans', image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150' },
                        { name: 'Mark Ruffalo', image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150' },
                        { name: 'Chris Hemsworth', image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1?w=150' }
                    ],
                    trailerUrl: 'assets/trailer/avengers.mp4'
                },
                'The Home': {
                    poster: 'assets/images/poster/image2.jpg',
                    bgImage: 'assets/images/poster/image2.jpg',
                    title: 'The Home',
                    synopsis: 'A troubled young man working at a retirement home uncovers terrifying secrets hidden within the facility, leading to a chilling fight for survival.',
                    rating: '8.7',
                    ageRating: 'R',
                    duration: '2h 15m',
                    genre: 'Action / Thriller',
                    releaseDate: 'October 15, 2025',
                    director: 'James Wan',
                    cast: [
                        { name: 'Pete Davidson', image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150' },
                        { name: 'Marisa Tomei', image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150' }
                    ],
                    trailerUrl: 'assets/trailer/thehome.mp4'
                },
                'Love You Long Time': {
                    poster: 'assets/images/poster/image3.jpg',
                    bgImage: 'assets/images/poster/image3.jpg',
                    title: 'Love You Long Time',
                    synopsis: 'A romantic drama about two people who unexpectedly reconnect and discover that love can endure despite distance, time, and life\'s challenges.',
                    rating: '8.9',
                    ageRating: 'PG-13',
                    duration: '2h 05m',
                    genre: 'Romance / Drama',
                    releaseDate: 'February 14, 2025',
                    director: 'JP Habac',
                    cast: [
                        { name: 'Carlo Aquino', image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1?w=150' },
                        { name: 'Eisley Cruz', image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150' }
                    ],
                    trailerUrl: 'assets/trailer/loveyoulongtime.mp4'
                },
                'Sputnik': {
                    poster: 'assets/images/poster/image4.jpg',
                    bgImage: 'assets/images/poster/image4.jpg',
                    title: 'Sputnik',
                    synopsis: 'After a mysterious space mission, a Soviet cosmonaut returns to Earth carrying a dangerous alien organism, forcing scientists to confront a terrifying extraterrestrial threat.',
                    rating: '8.5',
                    ageRating: 'PG-13',
                    duration: '2h 30m',
                    genre: 'Sci-Fi / Action',
                    releaseDate: 'August 14, 2020',
                    director: 'Egor Abramenko',
                    cast: [
                        { name: 'Oksana Akinshina', image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150' },
                        { name: 'Pyotr Fyodorov', image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150' }
                    ],
                    trailerUrl: 'assets/trailer/sputnik.mp4'
                },
                'Jurassic World Rebirth': {
                    poster: 'assets/images/poster/image5.jpg',
                    bgImage: 'assets/images/poster/image5.jpg',
                    title: 'Jurassic World Rebirth',
                    synopsis: 'Dinosaurs once again threaten humanity in an epic adventure. A new chapter in the Jurassic saga brings ancient creatures back to life and forces humanity to rethink playing god.',
                    rating: '8.8',
                    ageRating: 'PG-13',
                    duration: '2h 45m',
                    genre: 'Adventure / Action',
                    releaseDate: 'May 14, 2026',
                    director: 'Gareth Edwards',
                    cast: [
                        { name: 'Scarlett Johansson', image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150' },
                        { name: 'Jonathan Bailey', image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150' },
                        { name: 'Mahershala Ali', image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1?w=150' }
                    ],
                    trailerUrl: 'assets/trailer/jurassic.mp4'
                },
                'The Sheep Detectives': {
                    poster: 'assets/images/poster/image6.jpg',
                    bgImage: 'assets/images/poster/image6.jpg',
                    title: 'The Sheep Detectives',
                    synopsis: 'A clever team of sheep detectives uses disguises and teamwork to solve a mysterious farmyard crime.',
                    rating: '9.1',
                    ageRating: 'PG',
                    duration: '2h 12m',
                    genre: 'Comedy / Mystery',
                    releaseDate: 'November 20, 2025',
                    director: 'Kyle Balda',
                    cast: [
                        { name: 'Hugh Jackman', image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150' },
                        { name: 'Emma Thompson', image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150' }
                    ],
                    trailerUrl: 'assets/trailer/sheepdetectives.mp4'
                },
                'F1': {
                    poster: 'assets/images/poster/image7.jpg',
                    bgImage: 'assets/images/poster/image7.jpg',
                    title: 'F1',
                    synopsis: 'High-speed racing, fierce rivalries, and the pursuit of victory as a former driver returns to Formula 1 to mentor a rising young star.',
                    rating: '8.6',
                    ageRating: 'PG',
                    duration: '2h 32m',
                    genre: 'Documentary / Sports',
                    releaseDate: 'June 25, 2025',
                    director: 'Joseph Kosinski',
                    cast: [
                        { name: 'Brad Pitt', image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150' },
                        { name: 'Damson Idris', image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1?w=150' },
                        { name: 'Javier Bardem', image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150' }
                    ],
                    trailerUrl: 'assets/trailer/f1.mp4'
                },
                'The Fantastic Four: First Steps': {
                    poster: 'assets/images/poster/image8.jpg',
                    bgImage: 'assets/images/poster/image8.jpg',
                    title: 'The Fantastic Four: First Steps',
                    synopsis: 'Marvel\'s first family begins an exciting new adventure together. Four explorers discover extraordinary powers following a cosmic accident.',
                    rating: '8.3',
                    ageRating: 'PG-13',
                    duration: '2h 40m',
                    genre: 'Superhero / Action',
                    releaseDate: 'July 25, 2025',
                    director: 'Matt Shakman',
                    cast: [
                        { name: 'Pedro Pascal', image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150' },
                        { name: 'Vanessa Kirby', image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150' },
                        { name: 'Joseph Quinn', image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1?w=150' },
                        { name: 'Ebon Moss-Bachrach', image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150' }
                    ],
                    trailerUrl: 'assets/trailer/fantastic4.mp4'
                }
            };

            // Fuzzy lookup to handle exact string matching or lowercasing
            function getMatchingMovieKey(title) {
                if (!title) return Object.keys(movieDetails)[0];
                const cleanTitle = title.trim().toLowerCase();
                const foundKey = Object.keys(movieDetails).find(key => key.toLowerCase() === cleanTitle);
                return foundKey || Object.keys(movieDetails)[0];
            }

            const activeMovieKey = getMatchingMovieKey(selectedMovieTitle);
            localStorage.setItem('movie', activeMovieKey);

            let selectedDate = '';
            let selectedHall = '';
            let selectedTime = '';
            let selectedPriceBase = 0;
            let selectedPrice = 0;
            const dateList = document.getElementById('date-list');

            function formatDateLabel(date) {
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric'
                });
            }

            function buildDateOptions() {
                const today = new Date();
                const options = [];
                for (let i = 0; i < 3; i++) {
                    const date = new Date(today);
                    date.setDate(date.getDate() + i);
                    const formatted = formatDateLabel(date);
                    const option = document.createElement('div');
                    option.className = 'select-item';
                    option.dataset.value = formatted;
                    option.innerHTML = `<span>${formatted}</span><div class="radio-indicator"></div>`;
                    dateList.appendChild(option);
                    options.push(option);
                }
                return options;
            }

            const dates = buildDateOptions();
            const halls = Array.from(document.querySelectorAll('#hall-list .select-item'));
            const times = Array.from(document.querySelectorAll('#time-list .select-item'));

            function setActive(items, clickedItem) {
                items.forEach(item => item.classList.remove('active'));
                clickedItem.classList.add('active');
            }

            function renderMovieDetails() {
                const details = movieDetails[activeMovieKey];
                const title = details.title;

                // Update Hero Background
                if (heroSection && details.bgImage) {
                    heroSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('${details.bgImage}')`;
                    heroSection.style.backgroundSize = 'cover';
                    heroSection.style.backgroundPosition = 'center';
                }

                moviePoster.src = details.poster;
                moviePoster.alt = title + ' Poster';
                movieTitleEl.textContent = title;
                bookingTitle.textContent = 'Book tickets for ' + title;
                heroRating.textContent = details.rating;
                heroAgeRating.textContent = details.ageRating;
                heroDuration.textContent = details.duration;
                heroGenreText.textContent = details.genre;
                heroSynopsis.textContent = details.synopsis;
                detailSynopsis.textContent = details.synopsis;
                directorNameEl.textContent = details.director;
                
                if (trailerLink) {
                    const sourceTag = trailerLink.querySelector('source');
                    if (sourceTag) sourceTag.src = details.trailerUrl;
                    trailerLink.load();
                    trailerLink.muted = true;
                }

                factReleaseDate.textContent = details.releaseDate;
                factDuration.textContent = details.duration;
                factRating.textContent = details.ageRating;
                factGenre.textContent = details.genre;
                factDirector.textContent = details.director;
                castGrid.innerHTML = details.cast.map(member => `
                    <div class="cast-card">
                        <img src="${member.image}" alt="${member.name}">
                        <p class="cast-name">${member.name}</p>
                    </div>
                `).join('');
            }

            function enableList(listElement, enabled) {
                if (enabled) {
                    listElement.classList.remove('disabled');
                    listElement.querySelectorAll('.select-item').forEach(item => item.classList.remove('disabled'));
                } else {
                    listElement.classList.add('disabled');
                    listElement.querySelectorAll('.select-item').forEach(item => item.classList.add('disabled'));
                }
            }

            const convenienceFeePerTicket = 25;

            function getTicketCounts() {
                const counts = JSON.parse(localStorage.getItem('ticketCounts') || '{}');
                return {
                    regular: parseInt(counts.regular) || 0,
                    senior: parseInt(counts.senior) || 0,
                    student: parseInt(counts.student) || 0,
                    pwd: parseInt(counts.pwd) || 0
                };
            }

            function getTicketsTotal() {
                const c = getTicketCounts();
                return c.regular + c.senior + c.student + c.pwd;
            }

            function computeFinalPrice() {
                const base = parseFloat(selectedPriceBase) || 350;
                const counts = getTicketCounts();
                const totalTickets = getTicketsTotal();
                const subtotal = base * totalTickets;
                const discount = (counts.senior + counts.pwd) * base * 0.2 + counts.student * base * 0.15;
                const convenienceTotal = convenienceFeePerTicket * totalTickets;
                return Math.round(subtotal - discount + convenienceTotal);
            }

            window.updateSummary = function() {
                const counts = getTicketCounts();
                summaryMovie.textContent = activeMovieKey;
                summaryDate.textContent = selectedDate || 'Choose a date';
                summaryHall.textContent = selectedHall || 'Choose a hall after selecting a date';
                summaryTime.textContent = selectedTime || 'Choose a time after selecting a hall';
                const totalTickets = getTicketsTotal();
                selectedPrice = computeFinalPrice();
                summaryPrice.textContent = selectedPrice ? '₱' + selectedPrice : '₱0';
                
                document.getElementById('counts-display').textContent = `${counts.regular} / ${counts.senior} / ${counts.student} / ${counts.pwd}`;
                
                const totalTicketsDisplay = document.getElementById('total-tickets-display');
                if (totalTicketsDisplay) totalTicketsDisplay.textContent = totalTickets;
                
                checkoutBtn.disabled = !(selectedDate && selectedHall && selectedTime && totalTickets > 0);
                checkoutBtn.classList.toggle('disabled', checkoutBtn.disabled);
                
                localStorage.setItem('tickets', String(totalTickets));
                localStorage.setItem('grandTotal', String(selectedPrice));
            };

            function saveBooking() {
                localStorage.setItem('movie', activeMovieKey);
                localStorage.setItem('date', selectedDate);
                localStorage.setItem('cinema', selectedHall);
                localStorage.setItem('time', selectedTime);
                localStorage.setItem('grandTotal', selectedPrice);
                
                try {
                    const regularInput = document.getElementById('countRegular');
                    const seniorInput = document.getElementById('countSenior');
                    const studentInput = document.getElementById('countStudent');
                    const pwdInput = document.getElementById('countPWD');
                    const counts = {
                        regular: parseInt(regularInput && regularInput.value) || 0,
                        senior: parseInt(seniorInput && seniorInput.value) || 0,
                        student: parseInt(studentInput && studentInput.value) || 0,
                        pwd: parseInt(pwdInput && pwdInput.value) || 0
                    };
                    localStorage.setItem('ticketCounts', JSON.stringify(counts));
                    const totalTickets = counts.regular + counts.senior + counts.student + counts.pwd;
                    localStorage.setItem('tickets', String(totalTickets));
                } catch (e) {
                    localStorage.setItem('tickets', '0');
                }
                localStorage.setItem('selectedSeats', JSON.stringify([]));

                try {
                    if (moviePoster && moviePoster.src) {
                        localStorage.setItem('moviePoster', moviePoster.src);
                    }
                } catch (e) {}
            }

            dates.forEach(dateItem => {
                dateItem.addEventListener('click', () => {
                    selectedDate = dateItem.dataset.value;
                    setActive(dates, dateItem);
                    selectedHall = '';
                    selectedTime = '';
                    selectedPrice = 0;
                    halls.forEach(hall => hall.classList.remove('active'));
                    times.forEach(time => time.classList.remove('active'));
                    enableList(hallList, true);
                    enableList(timeList, false);
                    updateSummary();
                });
            });

            if (dates.length > 0) {
                dates[0].classList.add('active');
                selectedDate = dates[0].dataset.value;
                enableList(hallList, true);
                updateSummary();
            }

            halls.forEach(hallItem => {
                hallItem.addEventListener('click', () => {
                    if (!selectedDate) return;
                    selectedHall = hallItem.dataset.value;
                    selectedPriceBase = parseInt(hallItem.dataset.price) || 350;
                    selectedTime = '';
                    setActive(halls, hallItem);
                    times.forEach(time => time.classList.remove('active'));
                    enableList(timeList, true);
                    updateSummary();
                });
            });

            times.forEach(timeItem => {
                timeItem.addEventListener('click', () => {
                    if (!selectedHall) return;
                    selectedTime = timeItem.dataset.time;
                    selectedPriceBase = parseInt(timeItem.dataset.price) || selectedPriceBase || 350;
                    setActive(times, timeItem);
                    updateSummary();
                });
            });

            const regularInput = document.getElementById('countRegular');
            const seniorInput = document.getElementById('countSenior');
            const studentInput = document.getElementById('countStudent');
            const pwdInput = document.getElementById('countPWD');

            function readCounts() {
                const counts = JSON.parse(localStorage.getItem('ticketCounts') || '{}');
                return {
                    regular: parseInt(counts.regular) || 0,
                    senior: parseInt(counts.senior) || 0,
                    student: parseInt(counts.student) || 0,
                    pwd: parseInt(counts.pwd) || 0
                };
            }

            function saveCounts() {
                const counts = {
                    regular: parseInt(regularInput && regularInput.value) || 0,
                    senior: parseInt(seniorInput && seniorInput.value) || 0,
                    student: parseInt(studentInput && studentInput.value) || 0,
                    pwd: parseInt(pwdInput && pwdInput.value) || 0,
                };
                localStorage.setItem('ticketCounts', JSON.stringify(counts));
                updateSummary();
            }

            if (regularInput) regularInput.addEventListener('change', saveCounts);
            if (seniorInput) seniorInput.addEventListener('change', saveCounts);
            if (studentInput) studentInput.addEventListener('change', saveCounts);
            if (pwdInput) pwdInput.addEventListener('change', saveCounts);

            const initialCounts = readCounts();
            if (regularInput) regularInput.value = initialCounts.regular || 0;
            if (seniorInput) seniorInput.value = initialCounts.senior || 0;
            if (studentInput) studentInput.value = initialCounts.student || 0;
            if (pwdInput) pwdInput.value = initialCounts.pwd || 0;

            checkoutBtn.addEventListener('click', () => {
                if (!selectedDate || !selectedHall || !selectedTime) {
                    alert('Please select a date, cinema hall, and time before booking.');
                    return;
                }
                updateSummary();
                saveBooking();
                window.location.href = 'seats.php';
            });

            renderMovieDetails();
            if (summaryMovie) summaryMovie.textContent = activeMovieKey;
            document.title = activeMovieKey + ' | Cinema Royale Booking';

            if (selectedDate) {
                enableList(hallList, true);
            } else {
                enableList(hallList, false);
            }
            enableList(timeList, false);
            updateSummary();
        })();
    </script>

    <script>
        // ===== DISCOUNT VERIFICATION SYSTEM =====
        (function() {
            let discountVerifications = {
                senior: { verified: false, idNumber: '', idImage: '' },
                student: { verified: false, idNumber: '', idImage: '' },
                pwd: { verified: false, idNumber: '', idImage: '' }
            };

            let currentPendingDiscount = null;
            let previousCounts = { regular: 0, senior: 0, student: 0, pwd: 0 };

            try {
                const saved = JSON.parse(localStorage.getItem('discountVerifications') || '{}');
                if (saved.senior) discountVerifications.senior = saved.senior;
                if (saved.student) discountVerifications.student = saved.student;
                if (saved.pwd) discountVerifications.pwd = saved.pwd;
            } catch (e) {}

            const verificationModalEl = document.getElementById('verificationModal');
            if (!verificationModalEl) return;

            const verificationModal = new bootstrap.Modal(verificationModalEl);

            const modalDiscountType = document.getElementById('modalDiscountType');
            const modalInstructions = document.getElementById('modalInstructions');
            const idNumberInput = document.getElementById('idNumberInput');
            const idImageInput = document.getElementById('idImageInput');
            const saveVerificationBtn = document.getElementById('saveVerificationBtn');
            const cancelVerificationBtn = document.getElementById('cancelVerificationBtn');
            const declineVerificationBtn = document.getElementById('declineVerificationBtn');

            function persistCountsToLocal() {
                const reg = document.getElementById('countRegular');
                const sen = document.getElementById('countSenior');
                const stu = document.getElementById('countStudent');
                const pwd = document.getElementById('countPWD');
                const counts = {
                    regular: parseInt(reg && reg.value) || 0,
                    senior: parseInt(sen && sen.value) || 0,
                    student: parseInt(stu && stu.value) || 0,
                    pwd: parseInt(pwd && pwd.value) || 0
                };
                localStorage.setItem('ticketCounts', JSON.stringify(counts));
                if (typeof window.updateSummary === 'function') window.updateSummary();
            }

            function handleDiscountChange(typeKey, typeLabel, inputElement) {
                const currentVal = parseInt(inputElement.value) || 0;

                if (currentVal > 0 && !discountVerifications[typeKey].verified) {
                    currentPendingDiscount = { key: typeKey, label: typeLabel, inputEl: inputElement, count: currentVal };

                    modalDiscountType.textContent = typeLabel;
                    modalInstructions.textContent = `Please upload a valid ${typeLabel} ID / Document to avail the discounted price.`;

                    idNumberInput.value = '';
                    idImageInput.value = '';

                    verificationModal.show();
                } else if (currentVal === 0) {
                    discountVerifications[typeKey] = { verified: false, idNumber: '', idImage: '' };
                    previousCounts[typeKey] = 0;
                    persistCountsToLocal();
                } else {
                    previousCounts[typeKey] = currentVal;
                    persistCountsToLocal();
                }
            }

            saveVerificationBtn.addEventListener('click', () => {
                if (!idNumberInput.value.trim()) {
                    alert('Please enter your ID number.');
                    return;
                }
                if (idImageInput.files.length === 0) {
                    alert('Please select an ID image to upload.');
                    return;
                }

                if (currentPendingDiscount) {
                    const key = currentPendingDiscount.key;
                    discountVerifications[key].verified = true;
                    discountVerifications[key].idNumber = idNumberInput.value.trim();
                    discountVerifications[key].idImage = idImageInput.files[0].name;

                    previousCounts[key] = currentPendingDiscount.count;
                    persistCountsToLocal();
                    localStorage.setItem('discountVerifications', JSON.stringify(discountVerifications));

                    verificationModal.hide();
                    currentPendingDiscount = null;
                }
            });

            function revertPendingDiscount() {
                if (currentPendingDiscount) {
                    const key = currentPendingDiscount.key;
                    currentPendingDiscount.inputEl.value = previousCounts[key] || 0;
                    currentPendingDiscount = null;
                }
            }

            cancelVerificationBtn.addEventListener('click', revertPendingDiscount);
            declineVerificationBtn.addEventListener('click', revertPendingDiscount);

            const regInput = document.getElementById('countRegular');
            const senInput = document.getElementById('countSenior');
            const stuInput = document.getElementById('countStudent');
            const pwdInput = document.getElementById('countPWD');

            if (regInput) {
                regInput.addEventListener('change', () => {
                    previousCounts.regular = parseInt(regInput.value) || 0;
                    persistCountsToLocal();
                });
            }
            if (senInput) senInput.addEventListener('change', () => handleDiscountChange('senior', 'Senior Citizen', senInput));
            if (stuInput) stuInput.addEventListener('change', () => handleDiscountChange('student', 'Student', stuInput));
            if (pwdInput) pwdInput.addEventListener('change', () => handleDiscountChange('pwd', 'PWD', pwdInput));

            window.persistDiscountCounts = persistCountsToLocal;
        })();
    </script>
    <script src="js/app.js"></script>

</body>
</html>