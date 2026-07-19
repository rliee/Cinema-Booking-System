<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <nav class="navbar navbar-dark">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="index.php"> Cinema Royale </a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="index.php#now-showing">Now Showing</a>
                <a href="index.php#promotions">Promotions</a>
                <a href="index.php#experience">About</a>
                <a href="index.php#contact">Contact</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="container">
        <h2 class="page-title">Payment</h2>

<div class="payment-layout-container">
    
    <!-- LEFT SIDE: Customer Information & QR Code Panel -->
    <div class="customer-info-column">
        <h3>Customer Information</h3>
        
        <div class="form-group">
            <label>First Name</label>
            <input type="text" class="form-input">
        </div>
        
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" class="form-input">
        </div>
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" class="form-input">
        </div>
        
        <div class="form-group">
            <label>Mobile Number</label>
            <input type="text" class="form-input">
        </div>
        
        <div class="ticket-breakdown">
            <label>Ticket Breakdown</label>
            <div class="breakdown-text">Regular: 1 / Senior: 0 / PWD: 0</div>
        </div>
        
        <div class="form-group">
            <label>Payment Method</label>
            <div class="static-payment-method">QR Code (GCash / Maya / QR Ph)</div>
        </div>

        <!-- Integrated QR Code System -->
        <div id="qrPaymentContainer" class="qr-container">
            <h3>SCAN TO PAY</h3>
            <p class="qr-instructions">Please scan the QR code below using your preferred e-wallet to pay <strong>₱375</strong>.</p>
            
            <div class="qr-code-wrapper">
                <img id="qrCodeImg" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=cinema-royale-pay" alt="Payment QR Code">
            </div>
            
            <div class="timer-wrapper">
                Code expires in: <span id="paymentTimer">05:00</span>
            </div>
            
            <button type="button" class="btn-verify" onclick="simulatePaymentVerification()">I have scanned and paid</button>
        </div>
    </div>
                <div class="col-lg-6">
                    <div class="summary">
                        <h4>Booking Details</h4>
                        <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
                            <img id="paymentPoster" src="" alt="Poster" class="movie-poster" style="width:80px;height:110px;">
                            <div>
                                <p style="margin:0"><span id="movie"></span></p>
                            </div>
                        </div>
                        <p> Date <span id="date"></span> </p>
                        <p> Time <span id="time"></span> </p>
                        <p> Cinema <span id="cinema"></span> </p>
                        <p> Seats <span id="seats"></span> </p>
                        <p> Tickets <span id="tickets"></span> </p>
                        <p> Customer Type <span id="customerType"></span> </p>
                        <p> Discount <span id="discount"></span> </p>
                        <p> Total Amount to Pay <span id="amountPay"></span> </p>
                    </div>
                    <button class="btn btn-confirm" onclick="confirmBooking()"> Confirm Booking </button>
                </div>
            </div>
        </div>
    </div>
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
        <a href="#now-showing">Now Showing</a>
        <a href="#promotions">Promotions</a>
        <a href="#experience">About Us</a>
        <a href="#contact">Contact</a>
    </div>
    </div>
    <div class="footer-section">
        <h5>CONTACT</h5>
        <p><i class="fa-solid fa-location-dot"></i> 📍 Trece Martires City, Cavite 4109</p>
        <p>📞 +63 (2) 8888-1234</p>
        <p>📧 <a href="mailto:hello@cinemaroyale.com" style="color:#ffc700;text-decoration:none;">hello@cinemaroyale.com</a></p>
    </div>
        <div class="footer-section">
            <h5>TEAM</h5>
            <p>Arliesienne Ansuas<br>Ron Andrei Castro<br>James Arnold Dutosme<br>Kier Bryant Levita<br>Kylle
            Jonathan Padua<br>Genesis Saliedo<br>Josiah Joshua Torrefiel</p>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-watermark">CINEMA ROYALE</div>
        <p class="footer-copyright">© 2026 Cinema Royale. All rights reserved.</p>
    </div>
</footer>

    <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.js"></script>

    <script>
        let timerInterval;

// Initialize the QR code as soon as the page loads
window.addEventListener('DOMContentLoaded', (event) => {
    generatePaymentQR();
});

function generatePaymentQR() {
    const qrCodeImg = document.getElementById('qrCodeImg');
    const amount = 375; // Pulled from your total amount to pay

    // Generate a secure payload pointing to a universal QR Ph / payment standard system
    const paymentPayload = `cinema-royale-pay://amt=${amount}&ref=CR-${Math.floor(100000 + Math.random() * 900000)}`;
    
    // Apply payload to the image generator
    qrCodeImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(paymentPayload)}`;
    
    // Start a 5-minute expiration timer immediately
    startTimer(300); 
}

function startTimer(duration) {
    clearInterval(timerInterval);
    let timer = duration, minutes, seconds;
    const display = document.getElementById('paymentTimer');
    
    timerInterval = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            clearInterval(timerInterval);
            display.textContent = "EXPIRED";
            alert("The payment QR code has expired. Refreshing page to generate a new transaction session.");
            window.location.reload();
        }
    }, 1000);
}

function simulatePaymentVerification() {
    alert("Payment verified successfully! Enjoy Avengers: Infinity War.");
    window.location.reload(); 
}
        document.getElementById("movie").textContent =
            localStorage.getItem("movie");

        document.getElementById("date").textContent =
            localStorage.getItem("date");

        document.getElementById("time").textContent =
            localStorage.getItem("time");

        document.getElementById("cinema").textContent =
            localStorage.getItem("cinema");

        document.getElementById("tickets").textContent =
            localStorage.getItem("tickets");

        function renderPricing() {
            const baseTicketPrice = 350;
            const counts = JSON.parse(localStorage.getItem('ticketCounts') || '{}');
            const regular = parseInt(counts.regular) || 0;
            const senior = parseInt(counts.senior) || 0;
            const pwd = parseInt(counts.pwd) || 0;
            const selectedSeats = JSON.parse(localStorage.getItem('selectedSeats') || '[]');
            const tickets = Math.max(selectedSeats.length, regular + senior + pwd);
            const convenience = 25 * tickets;
            const subtotal = tickets * baseTicketPrice;
            const discount = Math.round((senior + pwd) * baseTicketPrice * 0.2);
            const totalPay = subtotal - discount + convenience;

            const summaryText = `Regular: ${regular} / Senior: ${senior} / PWD: ${pwd}`;
            const customerSummaryEl = document.getElementById('customerTypeSummary');
            if (customerSummaryEl) customerSummaryEl.textContent = summaryText;

            document.getElementById("customerType").textContent = `${regular}R / ${senior}S / ${pwd}P`;
            document.getElementById("discount").textContent = "₱" + discount;
            document.getElementById("amountPay").textContent = "₱" + totalPay;

            // persist
            localStorage.setItem('discount', discount);
            localStorage.setItem('totalPrice', subtotal - discount);
            localStorage.setItem('grandTotal', totalPay);
        }

        renderPricing();

        const seats =
            JSON.parse(localStorage.getItem("selectedSeats")) || [];

        document.getElementById("seats").textContent =
            seats.length ? seats.join(", ") : 'None';

        // render poster in payment summary
        const paymentPoster = document.getElementById('paymentPoster');
        const poster = localStorage.getItem('moviePoster');
        if (paymentPoster) {
            paymentPoster.src = poster || 'poster/image1.jpg';
        }



        function confirmBooking() {

            const firstname = document.getElementById("firstname").value;
            const lastname = document.getElementById("lastname").value;
            const fullname = firstname + ' ' + lastname;
            const email = document.getElementById("email").value;
            const mobile = document.getElementById("mobile").value;

            const payment =
                document.getElementById("payment").value;

            if (fullname == "") {

                alert("Please enter your full name.");
                return;

            }

            if (email == "") {

                alert("Please enter your email.");
                return;

            }

            if (mobile == "") {

                alert("Please enter your mobile number.");
                return;

            }

            if (payment == "") {

                alert("Please select a payment method.");
                return;

            }

            alert(
                "🎉 Booking Confirmed!\n\n" +
                "Thank you, " + fullname + "!\n\n" +
                "Enjoy your movie."
            );

            // Clear all booking data

            localStorage.clear();

            // Return to homepage

            window.location.href = "index.php";

        }
    </script>

    <script src="js/app.js"></script>

</body>

</html>