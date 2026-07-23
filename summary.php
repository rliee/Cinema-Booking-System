<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Booking Summary</title>

	<link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css">

</head>

<body>

	<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
		<div class="container">
			<a class="navbar-brand d-flex align-items-center" href="index.php">
				<img src="logo/Logo.png" alt="Cinema Royale Logo" class="navbar-logo me-2" style="height: 5rem; width: auto;" />
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
					<div class="d-flex w-100 justify-content-center tex-center">
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

    <div class="container booking-section">
        <div class="row g-4" style="align-items: stretch;">
            <!-- Left Column: Booking Summary -->
            <div class="col-lg-6">
                <div class="booking-card" style="height: 100%; display: flex; flex-direction: column; padding: 40px;">
                    <h2 style="color: #ffc700; margin-bottom: 24px; font-weight: 700;">Booking Summary</h2>

                    <!-- Movie Info Box -->
                    <div class="movie-box">
                        <img id="summaryPoster" src="" alt="Movie Poster" style="width:120px;height:160px;object-fit:cover;border-radius:8px;flex-shrink:0;background:#111;">
                        <div class="movie-info">
                            <h3 id="movie" style="margin: 0; color: #fff; font-size: 18px; font-weight: 700;"></h3>
                            <p class="meta" style="margin: 8px 0 0 0; color: #a0a0a0; font-size: 14px;"><span id="time"></span> &nbsp; • &nbsp; <span id="date"></span></p>
                        </div>
                    </div>

                    <!-- Booking Details Section -->
                    <h4 class="section-label" style="margin-top: 24px; margin-bottom: 16px; color: #ffc700;">Booking Details</h4>
                    <div class="details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; margin-bottom: 24px;">
                        <div class="label" style="color: #888; font-size: 13px; font-weight: 600;">Date</div>
                        <div class="value" id="date-alt" style="color: #fff; font-size: 14px;"></div>

                        <div class="label" style="color: #888; font-size: 13px; font-weight: 600;">Time</div>
                        <div class="value" id="time-alt" style="color: #fff; font-size: 14px;"></div>

                        <div class="label" style="color: #888; font-size: 13px; font-weight: 600;">Cinema</div>
                        <div class="value" id="cinema" style="color: #fff; font-size: 14px;"></div>

                        <div class="label" style="color: #888; font-size: 13px; font-weight: 600;">Type</div>
                        <div class="value" style="color: #fff; font-size: 14px;"><span class="badge bg-warning text-dark" style="font-size: 11px;">STANDARD</span></div>

                        <div class="label" style="color: #888; font-size: 13px; font-weight: 600;">Tickets (R / S / St / P)</div>
                        <div class="value" id="counts-display" style="color: #fff; font-size: 14px;">0 / 0 / 0 / 0</div>
                    </div>

                    <!-- Selected Seats Section -->
                    <h4 class="section-label" style="margin-bottom: 12px; color: #ffc700;">Selected Seats</h4>
                    <div class="seats-box" style="flex: 1;">
                        <div id="seats" class="seat-list"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Price Breakdown -->
            <div class="col-lg-6">
                <div class="payment-card sidebar-card" style="height: 100%; display: flex; flex-direction: column; padding: 40px; margin: 0;">

					<!-- Tickets Purchased Count (Highlighted) -->
					<div class="row" style="margin-bottom: 20px; padding: 16px; border: 2px solid #ffc700; border-radius: 12px; background: rgba(255, 199, 0, 0.08);">
						<div class="col">
							<strong style="color: #ffc700; font-size: 15px; display: block; margin-bottom: 6px;">Total Tickets Purchased</strong>
							<p style="color: #ddd; font-size: 13px; margin: 0;">Regular: <strong style="color:#fff;"><span id="count-regular">0</span></strong> | Senior: <strong style="color:#fff;"><span id="count-senior">0</span></strong> | Student: <strong style="color:#fff;"><span id="count-student">0</span></strong> | PWD: <strong style="color:#fff;"><span id="count-pwd">0</span></strong></p>
						</div>
						<div class="col text-end">
							<strong style="font-size: 28px; color: #ffc700; display: block;"><span id="total-tickets">0</span></strong>
						</div>
					</div>

					<!-- Price Breakdown Content -->
					<div class="breakdown" style="flex: 1;">
						<!-- Subtotal -->
						<div class="row" style="margin-bottom: 12px; padding-bottom: 12px;">
							<div class="col" style="color: #ccc; font-size: 14px;">Subtotal (₱<span id="ticket-price">350</span> × <span id="tickets">1</span>)</div>
							<div class="col text-end" style="color: #fff; font-size: 14px; font-weight: 600;">₱<span id="break-subtotal">0</span></div>
						</div>

						<!-- Senior Discount -->
						<div class="row" style="margin-bottom: 12px; padding-bottom: 12px;">
							<div class="col" style="color: #ccc; font-size: 14px;">Senior Discount (20%)</div>
							<div class="col text-end" style="color: #4ade80; font-size: 14px; font-weight: 600;">-₱<span id="break-senior-discount">0</span></div>
						</div>

						<!-- Student Discount -->
						<div class="row" style="margin-bottom: 12px; padding-bottom: 12px;">
							<div class="col" style="color: #ccc; font-size: 14px;">Student Discount (15%)</div>
							<div class="col text-end" style="color: #4ade80; font-size: 14px; font-weight: 600;">-₱<span id="break-student-discount">0</span></div>
						</div>

						<!-- PWD Discount -->
						<div class="row" style="margin-bottom: 12px; padding-bottom: 12px;">
							<div class="col" style="color: #ccc; font-size: 14px;">PWD Discount (20%)</div>
							<div class="col text-end" style="color: #4ade80; font-size: 14px; font-weight: 600;">-₱<span id="break-pwd-discount">0</span></div>
						</div>

						<!-- Total Discount -->
						<div class="row" style="margin-bottom: 12px; padding: 12px 0; border-top: 1px solid #555; border-bottom: 1px solid #555;">
							<div class="col"><strong style="color: #fff; font-size: 14px;">Total Discount</strong></div>
							<div class="col text-end" style="color: #4ade80; font-size: 14px;"><strong>-₱<span id="break-total-discount">0</span></strong></div>
						</div>

						<!-- Convenience Fee -->
						<div class="row" style="margin-bottom: 12px; padding-bottom: 12px;">
							<div class="col" style="color: #ccc; font-size: 14px;">Convenience Fee (₱<span id="fee-per-ticket">25</span> × <span id="tickets-fee">1</span>)</div>
							<div class="col text-end" style="color: #fff; font-size: 14px; font-weight: 600;">₱<span id="break-convenience">0</span></div>
						</div>

						<!-- Total Amount -->
						<div class="row" style="padding: 14px 0; border-top: 2px solid #ffc700; border-bottom: 2px solid #ffc700; margin-top: 16px;">
							<div class="col"><strong style="font-size: 16px; color: #fff;">TOTAL AMOUNT</strong></div>
							<div class="col text-end"><strong style="font-size: 20px; color: #ffc700;">₱<span id="break-total">0</span></strong></div>
						</div>
					</div>

					<!-- Summary Details -->
					<div class="row" style="font-size: 12px; color: #888; margin-top: 16px; padding-top: 12px; border-top: 1px solid #444;">
						<div class="col-6" style="margin-bottom: 8px;">Subtotal:</div>
						<div class="col-6 text-end" style="margin-bottom: 8px; color: #ccc;">₱<span id="summary-subtotal">0</span></div>
						<div class="col-6" style="margin-bottom: 8px;">Total Discount:</div>
						<div class="col-6 text-end" style="margin-bottom: 8px; color: #4ade80;">-₱<span id="summary-discount">0</span></div>
						<div class="col-6">Convenience Fee:</div>
						<div class="col-6 text-end" style="color: #ccc;">₱<span id="summary-convenience">0</span></div>
					</div>

					<!-- Action Buttons -->
					<div style="margin-top: 20px;">
						<button class="btn btn-continue" onclick="proceedPayment()" style="width: 100%; padding: 12px; margin-bottom: 12px; background-color: #ffc700; color: #000; border: none; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer;">Continue to Payment</button>

						<button class="btn btn-cancel" onclick="cancelBooking()" style="width: 100%; padding: 10px; background-color: transparent; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer;">← Cancel Booking</button>
						<p class="small text-muted" style="text-align: center; margin-top: 12px; color: #888; font-size: 12px;">🔒 Secure checkout powered by Stripe</p>
					</div>
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
		// Basic fields
		const movieEl = document.getElementById("movie");
		const dateEl = document.getElementById("date");
		const timeEl = document.getElementById("time");
		const cinemaEl = document.getElementById("cinema");

		movieEl.textContent = localStorage.getItem("movie") || "";
		dateEl.textContent = localStorage.getItem("date") || "";
		timeEl.textContent = localStorage.getItem("time") || "";
		cinemaEl.textContent = localStorage.getItem("cinema") || "";

		// Mirror for detail cards (guard for missing nodes)
		const dateAlt = document.getElementById("date-alt");
		const timeAlt = document.getElementById("time-alt");
		const cinemaMirror = document.getElementById('cinema');
		if (dateAlt) dateAlt.textContent = dateEl.textContent;
		if (timeAlt) timeAlt.textContent = timeEl.textContent;
		if (cinemaMirror) cinemaMirror.textContent = cinemaEl.textContent;

		// Seats rendering as badges (keeps stored data intact)
		const seats = JSON.parse(localStorage.getItem("selectedSeats")) || [];
		const seatsContainer = document.getElementById("seats");
		seatsContainer.innerHTML = "";
		if (seats.length) {
			seats.forEach(s => {
				const span = document.createElement('div');
				span.className = 'seat';
				span.textContent = s;
				seatsContainer.appendChild(span);
			});
		} else {
			seatsContainer.textContent = 'No seats selected';
		}

		// render poster if available
		const summaryPoster = document.getElementById('summaryPoster');
		const posterSrc = localStorage.getItem('moviePoster');
		if (summaryPoster) {
			if (posterSrc) {
				summaryPoster.src = posterSrc;
			} else {
				summaryPoster.src = 'poster/image1.jpg';
			}
		}

		// Pricing
		const baseTicketPrice = 350;
		const convenienceFeePerTicket = 25;
		const seniorDiscountRate = 0.20; // 20% discount for seniors
		const pwdDiscountRate = 0.20; // 20% discount for PWD

		function getTicketCounts() {
			const counts = JSON.parse(localStorage.getItem('ticketCounts') || '{}');
			return {
				regular: parseInt(counts.regular) || 0,
				senior: parseInt(counts.senior) || 0,
				student: parseInt(counts.student) || 0,
				pwd: parseInt(counts.pwd) || 0
			};
		}

		function updatePricing() {
			const counts = getTicketCounts();
			const seatCount = seats.length;
			const countsTotal = counts.regular + counts.senior + counts.student + counts.pwd;
			const ticketCount = seatCount || countsTotal;

			// Calculate subtotal (all tickets at base price)
			const subtotal = ticketCount * baseTicketPrice;

			// Calculate individual discounts
			const seniorDiscount = counts.senior * baseTicketPrice * seniorDiscountRate;
			const studentDiscount = counts.student * baseTicketPrice * 0.15;
			const pwdDiscount = counts.pwd * baseTicketPrice * pwdDiscountRate;
			const totalDiscount = seniorDiscount + studentDiscount + pwdDiscount;

			// Calculate convenience fee (applies to all tickets)
			const convenienceTotal = ticketCount * convenienceFeePerTicket;

			// Calculate final amount
			const finalAmount = subtotal - totalDiscount + convenienceTotal;

			// Update display elements for ticket counts
			document.getElementById("count-regular").textContent = counts.regular;
			document.getElementById("count-senior").textContent = counts.senior;
			document.getElementById("count-student").textContent = counts.student;
			document.getElementById("count-pwd").textContent = counts.pwd;
			document.getElementById("total-tickets").textContent = ticketCount;

			// Update price breakdown
			document.getElementById("tickets").textContent = ticketCount;
			document.getElementById("tickets-fee").textContent = ticketCount;
			document.getElementById("ticket-price").textContent = baseTicketPrice;
			document.getElementById("fee-per-ticket").textContent = convenienceFeePerTicket;

			// Update subtotal and discount amounts
			document.getElementById("break-subtotal").textContent = subtotal.toFixed(2);
			document.getElementById("break-senior-discount").textContent = seniorDiscount.toFixed(2);
			document.getElementById("break-pwd-discount").textContent = pwdDiscount.toFixed(2);
			document.getElementById("break-total-discount").textContent = totalDiscount.toFixed(2);
			document.getElementById("break-convenience").textContent = convenienceTotal.toFixed(2);
			document.getElementById("break-total").textContent = finalAmount.toFixed(2);

			// Update summary details
			document.getElementById("summary-subtotal").textContent = subtotal.toFixed(2);
			document.getElementById("summary-discount").textContent = totalDiscount.toFixed(2);
			document.getElementById("summary-convenience").textContent = convenienceTotal.toFixed(2);

			// Update counts display
			document.getElementById("counts-display").textContent = `${counts.regular} / ${counts.senior} / ${counts.student} / ${counts.pwd}`;

			// Store values for payment page
			localStorage.setItem("subtotal", subtotal.toFixed(2));
			localStorage.setItem("seniorDiscount", seniorDiscount.toFixed(2));
			localStorage.setItem("studentDiscount", studentDiscount.toFixed(2));
			localStorage.setItem("pwdDiscount", pwdDiscount.toFixed(2));
			localStorage.setItem("totalDiscount", totalDiscount.toFixed(2));
			localStorage.setItem("convenienceFee", convenienceTotal.toFixed(2));
			localStorage.setItem("finalAmount", finalAmount.toFixed(2));
			localStorage.setItem("ticketCount", ticketCount);
		}

		function proceedPayment() {
			window.location.href = "payment.php";
		}

		function cancelBooking() {
			if (confirm('Are you sure you want to cancel this booking? All selections will be cleared.')) {
				localStorage.removeItem('movie');
				localStorage.removeItem('date');
				localStorage.removeItem('time');
				localStorage.removeItem('cinema');
				localStorage.removeItem('ticketCounts');
				localStorage.removeItem('selectedSeats');
				localStorage.removeItem('tickets');
				localStorage.removeItem('grandTotal');
				localStorage.removeItem('moviePoster');
				window.location.href = 'index.php';
			}
		}

		updatePricing();
	</script>

	<script src="js/app.js"></script>

</body>

</html>