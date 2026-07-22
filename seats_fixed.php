<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection</title>

    <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="seatplan.css">


</head>

<body>

    <nav class="navbar navbar-dark">

        <div class="container d-flex justify-content-between align-items-center">

            <a class="navbar-brand" href="index.php">

                Cinema Royale

            </a>

            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="index.php#now-showing">Now Showing</a>
                <a href="index.php#promotions">Promotions</a>
                <a href="index.php#experience">About</a>
                <a href="index.php#contact">Contact</a>
            </div>

        </div>

    </nav>


    <div class="container-seat">

        <h2>Select Your Seats</h2>

        <div class="seat-column">

            <div class="booking-layout">

    <!-- LEFT SIDE -->
    <div style="flex:1;">

        <div class="screen">
            SCREEN
        </div>

        <div class="legend">

            <div class="legend-item">
                <div class="box available"></div>
                Available
            </div>

            <div class="legend-item">
                <div class="box selected"></div>
                Selected
            </div>

        </div>

        <div class="seat-layout" id="seatLayout"></div>

    </div>

    <!-- RIGHT SIDE -->
    <!-- Booking Summary -->
    <div class="summary">

        <h3>Booking Summary</h3>
        

                <div class="row">

                    <div class="col-md-6">

                        <p>

                            <strong>Movie:</strong>

                            <span id="movieName"></span>

                        </p>

                        <p>

                            <strong>Date:</strong>

                            <span id="movieDate"></span>

                        </p>

                        <p>

                            <strong>Time:</strong>

                            <span id="movieTime"></span>

                        </p>

                    </div>

                    <div class="col-md-6">

                        <p>

                            <strong>Cinema:</strong>

                            <span id="movieCinema"></span>

                        </p>

                        <p>
                            <strong>Tickets:</strong>
                            <span style="margin-left:8px">
                                <input id="countRegular" class="ticket-count" type="number" min="0" value="0" />
                                <input id="countSenior" class="ticket-count" type="number" min="0" value="0" />
                                <input id="countPWD" class="ticket-count" type="number" min="0" value="0" />
                            </span>
                        </p>

                        <p>

                            <strong>Price per Ticket:</strong>

                            ₱350

                        </p>

                    </div>

                </div>

                <hr class="text-secondary">

                <p>

                    <strong>Selected Seats:</strong>

                    <span id="seatList">

                        None

                    </span>

                </p>

                <p>

                    <strong>Total Selected:</strong>

                    <span id="selectedCount">

                        0

                    </span>

                </p>

                <p>

                    <strong>Total Amount:</strong>

                    ₱<span id="grandTotal">

                        0

                    </span>

                </p>

                <button class="btn btn-next" id="continueBtn">

                    Continue to Booking Summary →

                </button>

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
        const movie = localStorage.getItem("movie");
        const date = localStorage.getItem("date");
        const time = localStorage.getItem("time");
        const cinema = localStorage.getItem("cinema");

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
        const tickets = getTicketsTotal() || 1;
        const baseTicketPrice = 350;
        const convenienceFeePerTicket = 25;

        const movieNameElement = document.getElementById("movieName");
        const movieDateElement = document.getElementById("movieDate");
        const movieTimeElement = document.getElementById("movieTime");
        const movieCinemaElement = document.getElementById("movieCinema");
        const ticketCountElement = document.getElementById("ticketCount");

        if (movieNameElement) movieNameElement.textContent = movie;
        if (movieDateElement) movieDateElement.textContent = date;
        if (movieTimeElement) movieTimeElement.textContent = time;
        if (movieCinemaElement) movieCinemaElement.textContent = cinema;
        if (ticketCountElement) ticketCountElement.textContent = tickets;

        // initialize count inputs from storage
        const cntReg = document.getElementById('countRegular');
        const cntSen = document.getElementById('countSenior');
        const cntPWD = document.getElementById('countPWD');
        const storedCounts = getTicketCounts();
        if (cntReg) cntReg.value = storedCounts.regular || 0;
        if (cntSen) cntSen.value = storedCounts.senior || 0;
        if (cntPWD) cntPWD.value = storedCounts.pwd || 0;

function persistCountsFromInputs() {
            const counts = {
                regular: parseInt(cntReg && cntReg.value) || 0,
                senior: parseInt(cntSen && cntSen.value) || 0,
                student: storedCounts.student || 0,
                pwd: parseInt(cntPWD && cntPWD.value) || 0
            };
            localStorage.setItem('ticketCounts', JSON.stringify(counts));
            if (ticketCountElement) ticketCountElement.textContent = getTicketsTotal();
        }
        if (cntReg) cntReg.addEventListener('change', persistCountsFromInputs);
        if (cntSen) cntSen.addEventListener('change', persistCountsFromInputs);
        if (cntPWD) cntPWD.addEventListener('change', persistCountsFromInputs);



        const seatLayout=document.getElementById('seatLayout');
const rows=['A','B','C','D','E','F','G','H','I','J','K','L'];
rows.forEach((r,ri)=>{const row=document.createElement('div');row.className='seat-row';for(let i=1;i<=12;i++){if(i===7){const a=document.createElement('div');a.className='aisle';row.appendChild(a);}const s=document.createElement('div');s.className='seat';s.textContent=r+i;row.appendChild(s);}seatLayout.appendChild(row);if(ri===5){const g=document.createElement('div');g.style.height='28px';seatLayout.appendChild(g);}});
const seatElements=[...document.querySelectorAll('.seat')];
        const seatList = document.getElementById('seatList');
        const selectedCount = document.getElementById('selectedCount');
        const grandTotal = document.getElementById('grandTotal');
        const continueBtn = document.getElementById('continueBtn');

        function getSavedSeats() {
            try {
                const saved = JSON.parse(localStorage.getItem('selectedSeats') || '[]');
                return Array.isArray(saved) ? saved : [];
            } catch (e) {
                return [];
            }
        }

        function loadSelectedSeats() {
            const savedSeats = getSavedSeats();
            document.querySelectorAll('.seat.selected').forEach(seat => seat.classList.remove('selected'));
            const maxTickets = getTicketsTotal() || 1;
            let count = 0;

            savedSeats.forEach(label => {
                if (count >= maxTickets) return;
                const matchingSeat = Array.from(document.querySelectorAll('.seat:not(.taken)')).find(s => s.textContent.trim() === label);
                if (matchingSeat) {
                    matchingSeat.classList.add('selected');
                    count += 1;
                }
            });
        }

        function updateSummary() {
            const selectedSeats = Array.from(document.querySelectorAll('.seat.selected')).map(seat => seat.textContent.trim());

            if (seatList) {
                seatList.textContent = selectedSeats.length ? selectedSeats.join(', ') : 'None';
            }
            if (selectedCount) {
                selectedCount.textContent = selectedSeats.length;
            }

            const counts = getTicketCounts();
            const subtotal = selectedSeats.length * baseTicketPrice;
            const discount = Math.round((counts.senior + counts.pwd) * baseTicketPrice * 0.2);
            const convenience = convenienceFeePerTicket * selectedSeats.length;
            const finalTotal = subtotal - discount + convenience;

            if (grandTotal) {
                grandTotal.textContent = finalTotal;
            }

            localStorage.setItem('selectedSeats', JSON.stringify(selectedSeats));
            localStorage.setItem('seatCount', String(selectedSeats.length));
            localStorage.setItem('discount', String(discount));
            localStorage.setItem('totalPrice', String(subtotal - discount));
            localStorage.setItem('grandTotal', String(finalTotal));
        }

        function toggleSeatSelection(seat) {
            if (!seat || seat.classList.contains('taken')) return;

            const currentSelected = document.querySelectorAll('.seat.selected').length;
            const maxTickets = getTicketsTotal();
            const isSelected = seat.classList.contains('selected');

            if (maxTickets <= 0) {
                alert('Please select ticket quantities first.');
                return;
            }

            if (!isSelected && currentSelected >= maxTickets) {
                alert('You can only select ' + maxTickets + ' seat(s).');
                return;
            }

            seat.classList.toggle('selected');
            updateSummary();
        }

        seatElements.forEach(seat => {
            seat.setAttribute('role', 'button');
            seat.setAttribute('tabindex', '0');
            seat.addEventListener('click', () => toggleSeatSelection(seat));
            seat.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    toggleSeatSelection(seat);
                }
            });
        });

        if (continueBtn) {
            continueBtn.addEventListener('click', function() {
                const selectedSeatsCount = document.querySelectorAll('.seat.selected').length;
                const required = getTicketsTotal();
                if (required <= 0) {
                    alert('Please add ticket quantities before continuing.');
                    return;
                }
                if (selectedSeatsCount !== required) {
                    alert('Please select exactly ' + required + ' seat(s).');
                    return;
                }
                window.location.href = 'summary.php';
            });
        }

        function updateCountsAndSummary() {
            persistCountsFromInputs();
            const selectedSeats = Array.from(document.querySelectorAll('.seat.selected'));
            const maxTickets = getTicketsTotal();
            if (selectedSeats.length > maxTickets) {
                selectedSeats.slice(maxTickets).forEach(seat => seat.classList.remove('selected'));
            }
            updateSummary();
        }
        if (cntReg) cntReg.addEventListener('input', updateCountsAndSummary);
        if (cntSen) cntSen.addEventListener('input', updateCountsAndSummary);
        if (cntPWD) cntPWD.addEventListener('input', updateCountsAndSummary);

        loadSelectedSeats();
        updateSummary();

            </script>

    <script src="js/app.js"></script>

</body>

</html>