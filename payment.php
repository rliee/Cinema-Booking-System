<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="libraries/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="logo/Logo.png" alt="Cinema Royale Logo" class="navbar-logo me-2" style="height: 40px; width: auto;"/>
                <div> Cinema Royale
                    <div class="navbar-brand-subtitle">PREMIUM EXPERIENCE</div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
<ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#now-showing">Now Showing</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#promotions">Promotions</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#experience">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
                </ul>
                <div class="auth-buttons ms-auto">
                    <a href="api/login.php" class="auth-btn login-btn">Login</a>
                    <a href="api/signup.php" class="auth-btn register-btn">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container payment-page">
        <h2 class="page-title">Payment</h2>

        <div class="payment-split-layout">

            <!-- LEFT: QR Code Panel -->
            <div class="qr-panel">
                <div class="qr-panel-inner">
                    <div class="qr-header">
                        <span class="qr-icon">📱</span>
                        <h3>SCAN TO PAY</h3>
                    </div>
                    <p class="qr-instructions">Scan the QR code using GCash, Maya, or any QR Ph app</p>

                    <div class="qr-code-wrapper">
                        <img id="qrCodeImg" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=cinema-royale-pay" alt="Payment QR Code">
                    </div>

                    <div class="qr-meta">
                        <div class="qr-ref">
                            <span class="meta-label">Reference No:</span>
                            <span class="meta-value" id="referenceNumber">---</span>
                        </div>
                        <div class="qr-timer">
                            <span class="meta-label">Expires in:</span>
                            <span class="meta-value timer-value" id="paymentTimer">10:00</span>
                        </div>
                    </div>

                    <div class="qr-amount">₱<span id="amountPay">0</span></div>
                </div>
            </div>

            <!-- RIGHT: Reference Number Form -->
            <div class="ref-form-panel">
                <div class="ref-form-inner">
                    <div class="ref-form-header">
                        <span class="ref-form-icon">✏️</span>
                        <h3>Confirm Payment</h3>
                        <p>Enter the reference number from your e-wallet</p>
                    </div>

                    <div class="ref-form-body">
                        <div class="ref-form-group">
                            <label for="userRefInput">E-Wallet Reference Number</label>
                            <div class="ref-input-container">
                                <input type="text" id="userRefInput" class="ref-input" placeholder="e.g. GCASH-1234567890" autocomplete="off">
                                <span class="input-icon">🔑</span>
                            </div>
                            <span class="ref-hint">This is found in your e-wallet transaction history</span>
                        </div>

                        <button type="button" class="btn-pay-confirm" onclick="confirmPayment()">
                            <span class="btn-icon">✓</span>
                            Confirm Payment
                        </button>

                        <div class="ref-secure-notice">
                            <span class="secure-icon">🔒</span>
                            Your information is secure and encrypted
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer id="contact">
        <div class="container">
            <div class="footer-section">
                <h5>Cinema Royale</h5>
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
                    <a href="#now-showing">Now Showing</a>
                    <a href="#promotions">Promotions</a>
                    <a href="#experience">About Us</a>
                    <a href="#contact">Contact</a>
                </div>
            </div>
            <div class="footer-section">
                <h5>CONTACT</h5>
                <p><i class="fa-solid fa-location-dot"></i> Trece Martires City, Cavite 4109</p>
                <p>+63 (2) 8888-1234</p>
                <p><a href="mailto:hello@cinemaroyale.com" style="color:#ffc700;text-decoration:none;">hello@cinemaroyale.com</a></p>
            </div>
            <div class="footer-section">
                <h5>TEAM</h5>
                <p>Arliesienne Ansuas<br>Ron Andrei Castro<br>James Arnold Dutosme<br>Kier Bryant Levita<br>Kylle Jonathan Padua<br>Genesis Saliedo<br>Josiah Joshua Torrefiel</p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-watermark">CINEMA ROYALE</div>
            <p class="footer-copyright">&copy; 2026 Cinema Royale. All rights reserved.</p>
        </div>
    </footer>

<script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.js"></script>

    <script>
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

        let timerInterval;

        window.addEventListener('DOMContentLoaded', function() {
            generatePaymentQR();
            renderPricing();
        });

        function generatePaymentQR() {
            const qrCodeImg = document.getElementById('qrCodeImg');
            const refDisplay = document.getElementById('referenceNumber');
            const amount = 375;

            const refNumber = 'CR-' + (Math.floor(100000 + Math.random() * 900000));
            const paymentPayload = 'cinema-royale-pay://amt=' + amount + '&ref=' + refNumber;

            qrCodeImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(paymentPayload);

            if (refDisplay) {
                refDisplay.textContent = refNumber;
            }

            startTimer(600);
        }

        function startTimer(duration) {
            clearInterval(timerInterval);
            var timer = duration, minutes, seconds;
            var display = document.getElementById('paymentTimer');

            timerInterval = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                display.textContent = minutes + ':' + seconds;

                if (--timer < 0) {
                    clearInterval(timerInterval);
                    display.textContent = 'EXPIRED';
                    alert('The payment QR code has expired. Refreshing page to generate a new transaction session.');
                    window.location.reload();
                }
            }, 1000);
        }

        function confirmPayment() {
            var refInput = document.getElementById('userRefInput').value.trim();

            if (refInput === '') {
                alert('Please enter your e-wallet reference number to confirm payment.');
                return;
            }

            alert('Payment confirmed! Reference No: ' + refInput + '\n\nEnjoy your movie!');

            localStorage.clear();
            window.location.href = 'index.php';
        }

        function renderPricing() {
            var baseTicketPrice = 350;
            var counts = JSON.parse(localStorage.getItem('ticketCounts') || '{}');
            var regular = parseInt(counts.regular) || 0;
            var senior = parseInt(counts.senior) || 0;
            var student = parseInt(counts.student) || 0;
            var pwd = parseInt(counts.pwd) || 0;
            var selectedSeats = JSON.parse(localStorage.getItem('selectedSeats') || '[]');
            var tickets = Math.max(selectedSeats.length, regular + senior + student + pwd);
            var convenience = 25 * tickets;
            var subtotal = tickets * baseTicketPrice;
            var discount = Math.round((senior + pwd) * baseTicketPrice * 0.2 + student * baseTicketPrice * 0.15);
            var totalPay = subtotal - discount + convenience;

            var amountPayEl = document.getElementById('amountPay');
            if (amountPayEl) amountPayEl.textContent = totalPay;
        }
    </script>

    <script src="js/app.js"></script>

    <!-- Session Handler Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loggedIn = localStorage.getItem("loggedIn");
            if (loggedIn === "true") {
                const authButtons = document.querySelector(".auth-buttons");
                if (authButtons) {
                    authButtons.innerHTML = `
                        <span class="welcome-text" style="color: #fff; margin-right: 15px;">Welcome Back!</span>
                        <a href="#" id="logout-btn" class="auth-btn login-btn">Logout</a>
                    `;
                    document.getElementById("logout-btn").addEventListener("click", function (e) {
                        e.preventDefault();
                        localStorage.removeItem("loggedIn");
                        window.location.href = "index.php";
                    });
                }
            }
        });
    </script>

</body>
</html>

