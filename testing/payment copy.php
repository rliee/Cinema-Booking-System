<?php

require_once __DIR__ . "/auth/session.php";
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/classes/PaymentRepository.php";

$paymentId = $_GET["id"];


if (!$paymentId) {
  header("Location: http://localhost/cinema-booking/");
}

$paymentService = new PaymentRepository($conn);
$payment = $paymentService->getPaymentByReference($paymentId);

if (!$payment) {
  header("Location: http://localhost/cinema-booking/");
}

$price = '₱' . number_format((int)$payment["amount"], 0, '.', ',');

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment - Cinema Royale</title>
  <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="libraries/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="css/index.css">

  <script>
    // (function() {
    //   if (localStorage.getItem("loggedIn") !== "true") {
    //     window.location.href = "index.php";
    //   }
    // })();
  </script>
</head>

<body>

  <!-- NAVBAR FIX -->
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
          <h5 class="modal-title">
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

  <!-- PAYMENT PAGE CONTENT FIX -->
  <div class="payment-page">
    <h2 class="page-title">Payment</h2>
    <div class="payment-split-layout">

      <!-- QR Panel -->
      <div class="qr-panel">
        <div class="qr-panel-inner">
          <div class="qr-header">
            <span class="qr-icon">📱</span>
            <h3 class="navbar-brand">SCAN TO PAY</h3>
          </div>
          <p class="qr-instructions">Scan the QR code using GCash, Maya, or any QR Ph app</p>
          <div class="qr-code-wrapper">
            <img id="qrCodeImg" src="assets/download.png" alt="Payment QR Code">
          </div>
          <div class="qr-amount"><span id=""><?php echo $price; ?></span></div>
          <div class="qr-meta">
            <!-- <div class="qr-ref">
              <span class="meta-label">Reference No:</span>
              <span class="meta-value" id="referenceNumber">---</span>
            </div> -->
            <!-- <div class="qr-timer">
              <span class="meta-label">Expires in:</span>
              <span class="meta-value timer-value" id="paymentTimer">10:00</span>
            </div> -->
          </div>

        </div>
      </div>

      <!-- Reference Form Panel -->
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
              <span class="btn-icon">✓</span> Confirm Payment
            </button>
            <div class="ref-secure-notice">
              <span class="secure-icon">🔒</span> Your information is secure and encrypted
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- FOOTER FIX -->

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


  <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const loggedIn = localStorage.getItem("loggedIn");
      const userName = localStorage.getItem("userName") || "User";

      if (loggedIn === "true") {
        const authButtons = document.querySelector(".auth-buttons");
        if (authButtons) {
          authButtons.innerHTML = `
                    <span class="welcome-text style="color: #fff; margin-right: 15px; font-weight: 600;">
                        Welcome, ${userName}!
                    </span>
                    <a href="#" id="logout-btn" class="auth-btn login-btn" style="text-decoration: none;">Logout</a>
                `;

          document.getElementById("logout-btn").addEventListener("click", function(e) {
            e.preventDefault();
            localStorage.removeItem("loggedIn");
            localStorage.removeItem("userName");
            localStorage.removeItem("userEmail");
            window.location.href = "index.php";
          });
        }
      }
    });
  </script>
  <script>
    document.querySelectorAll(".nav-link").forEach((link) => {
      link.addEventListener("click", () => {
        document.querySelector(".navbar-collapse")?.classList.remove("show");
      });
    });

    let lastScrollTop = 0;
    const header = document.querySelector(".navbar");
    window.addEventListener("scroll", function() {
      let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
      if (currentScroll > lastScrollTop && currentScroll > 50) {
        header.classList.add("hide-header");
      } else {
        header.classList.remove("hide-header");
      }
      lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });

    let timerInterval;
    window.addEventListener("DOMContentLoaded", function() {
      // generatePaymentQR();
      renderPricing();
    });

    function generatePaymentQR() {
      const qrCodeImg = document.getElementById("qrCodeImg");
      const refDisplay = document.getElementById("referenceNumber");
      var counts = JSON.parse(localStorage.getItem("ticketCounts") || "{}");
      var regular = parseInt(counts.regular) || 0;
      var senior = parseInt(counts.senior) || 0;
      var student = parseInt(counts.student) || 0;
      var pwd = parseInt(counts.pwd) || 0;
      var selectedSeats = JSON.parse(localStorage.getItem("selectedSeats") || "[]");
      var tickets = Math.max(selectedSeats.length, regular + senior + student + pwd);
      if (tickets === 0) tickets = 1;
      var convenience = 25 * tickets;
      var subtotal = tickets * 350;
      var discount = Math.round((senior + pwd) * 350 * 0.2 + student * 350 * 0.15);
      var amount = subtotal - discount + convenience;
      const refNumber = "CR-" + (Math.floor(100000 + Math.random() * 900000));
      const paymentPayload = "cinema-royale-pay://amt=" + amount + "&ref=" + refNumber;
      qrCodeImg.src = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodeURIComponent(paymentPayload);
      if (refDisplay) {
        refDisplay.textContent = refNumber;
      }
      // startTimer(600);
      var amountPayEl = document.getElementById("amountPay");
      if (amountPayEl) amountPayEl.textContent = amount;
    }

    function startTimer(duration) {
      clearInterval(timerInterval);
      var timer = duration,
        minutes, seconds;
      var display = document.getElementById("paymentTimer");
      timerInterval = setInterval(function() {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        display.textContent = minutes + ":" + seconds;
        if (--timer < 0) {
          clearInterval(timerInterval);
          display.textContent = "EXPIRED";
          alert("The payment QR code has expired. Reloading page...");
          window.location.reload();
        }
      }, 1000);
    }

    // UPDATED FUNCTION: Saves payment confirmation, clears booking session data, and redirects to index.php
    async function confirmPayment() {
      const inputElement = document.getElementById("userRefInput");

      if (!inputElement) {
        return;
      }

      const paymentReference = inputElement.value.trim();

      if (!paymentReference) {
        alert("Please enter your payment reference.");
        return;
      }


      const params = new URLSearchParams(window.location.search);

      const bookingReference = params.get("id");


      if (!bookingReference) {
        alert("Invalid booking reference.");
        return;
      }


      try {

        const response = await fetch("api/payments/pay.php", {
          method: "POST",

          headers: {
            "Content-Type": "application/json"
          },

          body: JSON.stringify({
            booking_reference: bookingReference,
            payment_reference: paymentReference
          })
        });


        const result = await response.json();


        if (result.success) {

          window.location.href =
            `transaction.php`;

        } else {

          alert(result.message);

        }


      } catch (error) {

        console.error("Payment error:", error);

        alert("Something went wrong.");

      }
    }

    function renderPricing() {
      var baseTicketPrice = 350;
      var counts = JSON.parse(localStorage.getItem("ticketCounts") || "{}");
      var regular = parseInt(counts.regular) || 0;
      var senior = parseInt(counts.senior) || 0;
      var student = parseInt(counts.student) || 0;
      var pwd = parseInt(counts.pwd) || 0;
      var selectedSeats = JSON.parse(localStorage.getItem("selectedSeats") || "[]");
      var tickets = Math.max(selectedSeats.length, regular + senior + student + pwd);
      var convenience = 25 * tickets;
      var subtotal = tickets * baseTicketPrice;
      var discount = Math.round((senior + pwd) * baseTicketPrice * 0.2 + student * baseTicketPrice * 0.15);
      var totalPay = subtotal - discount + convenience;
      var amountPayEl = document.getElementById("amountPay");
      if (amountPayEl) amountPayEl.textContent = totalPay;
    }
  </script>
  <script src="js/app.js"></script>
</body>

</html>