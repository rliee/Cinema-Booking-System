<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection - Cinema Royale</title>

    <link href="libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/seatplan.css">
    <link rel="stylesheet" href="libraries/fontawesome/css/all.min.css">

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

    <div class="container-seat">

        <h2>Select Your Seats</h2>

        <div class="booking-layout">

            <!-- LEFT SIDE: Seat Map -->
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
                    <div class="legend-item">
                        <div class="box unavailable"></div>
                        Booked
                    </div>
                </div>

                <div class="seat-layout" id="seatLayout"></div>

            </div>

            <!-- RIGHT SIDE: Booking Summary -->
            <div class="summary">

                <h3>Booking Summary</h3>

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

                <p>
                    <strong>Cinema:</strong>
                    <span id="movieCinema"></span>
                </p>

                <p>
                    <strong>Selected Seats:</strong>
                    <span id="selectedSeatsDisplay">None</span>
                </p>

                <p>
                    <strong>Tickets:</strong>
                    <span id="ticketCountDisplay">0 / 0</span>
                </p>

                <p>
                    <strong>Total:</strong>
                    <span id="seatTotalPrice">₱0</span>
                </p>

                <div class="summary-actions" style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                    <button class="btn-next" id="proceedToPaymentBtn" disabled>
                        <i class="fa-solid fa-arrow-right"></i> Proceed to Payment
                    </button>
                    <button class="btn-back" id="backToBookingBtn" style="background: transparent; color: #aaa; border: 1px solid #333; padding: 10px; border-radius: 8px; cursor: pointer; font-size: 14px;">
                        <i class="fa-solid fa-arrow-left"></i> Back to Booking
                    </button>
                </div>

            </div>

        </div>

    </div>

    <footer style="background: #0a0a0a; border-top: 1px solid rgba(255, 199, 0, 0.1); padding: 40px 0 20px; margin-top: 40px;">
        <div class="container">
            <div class="footer-bottom" style="text-align: center;">
                <div class="footer-watermark" style="font-size: 48px; font-weight: 700; color: rgba(255, 199, 0, 0.08); letter-spacing: 4px; margin-bottom: 16px;">CINEMA ROYALE</div>
                <p class="footer-copyright" style="color: #666; font-size: 13px;">&copy; 2026 Cinema Royale. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

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
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ====== CONFIGURATION ======
            const ROWS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            const SEATS_PER_ROW = 13;
            const AISLE_AFTER = [5, 9]; // Insert gap after these seat positions (1-indexed)
            
            let takenSeats = [];      // Seats already booked (fetched from API)
            let selectedSeats = [];   // Seats currently selected by user
            let maxSelectable = 0;    // Max number of seats user can select
            let basePrice = 350;      // Default ticket price
            
            // ====== DOM REFS ======
            const seatLayout = document.getElementById('seatLayout');
            const movieNameEl = document.getElementById('movieName');
            const movieDateEl = document.getElementById('movieDate');
            const movieTimeEl = document.getElementById('movieTime');
            const movieCinemaEl = document.getElementById('movieCinema');
            const selectedSeatsDisplay = document.getElementById('selectedSeatsDisplay');
            const ticketCountDisplay = document.getElementById('ticketCountDisplay');
            const seatTotalPrice = document.getElementById('seatTotalPrice');
            const proceedBtn = document.getElementById('proceedToPaymentBtn');
            const backBtn = document.getElementById('backToBookingBtn');
            
            // ====== LOAD BOOKING DATA FROM LOCALSTORAGE ======
            const movie = localStorage.getItem('movie') || 'Unknown';
            const date = localStorage.getItem('date') || '';
            const cinema = localStorage.getItem('cinema') || '';
            const time = localStorage.getItem('time') || '';
            
            // Get ticket counts
            let ticketCounts = { regular: 0, senior: 0, student: 0, pwd: 0 };
            try {
                const saved = JSON.parse(localStorage.getItem('ticketCounts') || '{}');
                ticketCounts = {
                    regular: parseInt(saved.regular) || 0,
                    senior: parseInt(saved.senior) || 0,
                    student: parseInt(saved.student) || 0,
                    pwd: parseInt(saved.pwd) || 0
                };
            } catch(e) {}
            
            const totalTickets = ticketCounts.regular + ticketCounts.senior + ticketCounts.student + ticketCounts.pwd;
            maxSelectable = totalTickets;
            
            // ====== UPDATE SUMMARY UI ======
            movieNameEl.textContent = movie;
            movieDateEl.textContent = date || 'Not selected';
            movieTimeEl.textContent = time || 'Not selected';
            movieCinemaEl.textContent = cinema || 'Not selected';
            
            // ====== GENERATE SEAT GRID ======
            function generateSeats() {
                seatLayout.innerHTML = '';
                
                ROWS.forEach(function(rowLetter) {
                    const rowDiv = document.createElement('div');
                    rowDiv.className = 'seat-row d-flex align-items-center justify-content-center mb-2';
                    
                    // Row label
                    const rowLabel = document.createElement('div');
                    rowLabel.style.cssText = 'width: 28px; font-weight: bold; color: #ffc700; text-align: center; font-size: 14px;';
                    rowLabel.textContent = rowLetter;
                    rowDiv.appendChild(rowLabel);
                    
                    for (let i = 1; i <= SEATS_PER_ROW; i++) {
                        // Add aisle spacer
                        if (AISLE_AFTER.includes(i)) {
                            const spacer = document.createElement('div');
                            spacer.style.cssText = 'width: 20px;';
                            rowDiv.appendChild(spacer);
                        }
                        
                        const seatId = rowLetter + i;
                        const seat = document.createElement('div');
                        seat.className = 'seat';
                        seat.dataset.seatId = seatId;
                        seat.textContent = i;
                        
                        // Check if seat is taken
                        if (takenSeats.includes(seatId)) {
                            seat.classList.add('taken');
                            seat.title = 'Already booked';
                        } else {
                            seat.addEventListener('click', handleSeatClick);
                        }
                        
                        rowDiv.appendChild(seat);
                    }
                    
                    seatLayout.appendChild(rowDiv);
                });
            }
            
            // ====== HANDLE SEAT CLICK ======
            function handleSeatClick(e) {
                const seat = e.currentTarget;
                const seatId = seat.dataset.seatId;
                
                // Don't allow clicking taken seats (safety check)
                if (seat.classList.contains('taken')) return;
                
                if (seat.classList.contains('selected')) {
                    // Deselect
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(function(s) { return s !== seatId; });
                } else {
                    // Check if max reached
                    if (maxSelectable > 0 && selectedSeats.length >= maxSelectable) {
                        alert('You can only select up to ' + maxSelectable + ' seat(s).');
                        return;
                    }
                    // Select
                    seat.classList.add('selected');
                    selectedSeats.push(seatId);
                }
                
                updateSummary();
            }
            
            // ====== UPDATE SUMMARY ======
            function updateSummary() {
                const count = selectedSeats.length;
                
                // Sort seats for display (by row letter then number)
                const sorted = selectedSeats.slice().sort(function(a, b) {
                    const rowA = a.charAt(0);
                    const rowB = b.charAt(0);
                    const numA = parseInt(a.substring(1));
                    const numB = parseInt(b.substring(1));
                    if (rowA !== rowB) return rowA.localeCompare(rowB);
                    return numA - numB;
                });
                
                selectedSeatsDisplay.textContent = sorted.length > 0 ? sorted.join(', ') : 'None';
                ticketCountDisplay.textContent = count + ' / ' + (maxSelectable || 'Unlimited');
                
                // Calculate price based on ticket type breakdown from booking page
                const regPrice = basePrice;
                const senPrice = Math.round(basePrice * 0.8);  // 20% off
                const stuPrice = Math.round(basePrice * 0.85); // 15% off
                const pwdPrice = Math.round(basePrice * 0.8);  // 20% off
                const convenienceFee = 25;
                
                const totalTicketTypes = ticketCounts.regular + ticketCounts.senior + ticketCounts.student + ticketCounts.pwd;
                
                let subtotal = 0;
                if (totalTicketTypes > 0 && count > 0) {
                    // Allocate seats proportionally to ticket types
                    const ratio = count / totalTicketTypes;
                    const regSeats = Math.min(Math.round(ticketCounts.regular * ratio), count);
                    const senSeats = Math.min(Math.round(ticketCounts.senior * ratio), count - regSeats);
                    const stuSeats = Math.min(Math.round(ticketCounts.student * ratio), count - regSeats - senSeats);
                    const pwdSeats = count - regSeats - senSeats - stuSeats;
                    
                    subtotal = (regSeats * regPrice) + 
                               (senSeats * senPrice) + 
                               (stuSeats * stuPrice) + 
                               (pwdSeats * pwdPrice);
                } else {
                    subtotal = count * regPrice;
                }
                
                const convenienceTotal = convenienceFee * count;
                const total = subtotal + convenienceTotal;
                
                seatTotalPrice.textContent = '₱' + total.toLocaleString();
                
                // Save to localStorage
                localStorage.setItem('selectedSeats', JSON.stringify(sorted));
                localStorage.setItem('grandTotal', String(total));
                localStorage.setItem('seatsCount', String(count));
                
                // Enable/disable proceed button
                proceedBtn.disabled = count === 0;
                proceedBtn.classList.toggle('disabled', count === 0);
            }
            
            // ====== FETCH TAKEN SEATS FROM API ======
            async function fetchTakenSeats() {
                if (!cinema || !date || !time) {
                    // No booking data, still generate seats normally
                    generateSeats();
                    updateSummary();
                    return;
                }
                
                try {
                    const params = new URLSearchParams({
                        cinema: cinema,
                        date: date,
                        time: time
                    });
                    
                    const response = await fetch('api/seats.php?' + params.toString());
                    const data = await response.json();
                    
                    if (data.success && data.takenSeats) {
                        takenSeats = data.takenSeats;
                    }
                } catch (e) {
                    console.warn('Could not load taken seats from API:', e);
                }
                
                generateSeats();
                updateSummary();
            }
            
            // ====== BACK BUTTON ======
            backBtn.addEventListener('click', function() {
                window.location.href = 'booking.php?movie=' + encodeURIComponent(movie);
            });
            
            // ====== PROCEED TO PAYMENT ======
            proceedBtn.addEventListener('click', function() {
                if (selectedSeats.length === 0) {
                    alert('Please select at least one seat.');
                    return;
                }
                
                // Final save of selected data
                const sorted = selectedSeats.slice().sort(function(a, b) {
                    const rowA = a.charAt(0);
                    const rowB = b.charAt(0);
                    const numA = parseInt(a.substring(1));
                    const numB = parseInt(b.substring(1));
                    if (rowA !== rowB) return rowA.localeCompare(rowB);
                    return numA - numB;
                });
                
                localStorage.setItem('selectedSeats', JSON.stringify(sorted));
                localStorage.setItem('seatsCount', String(sorted.length));
                
                window.location.href = 'payment.php';
            });
            
            // ====== INIT ======
            fetchTakenSeats();
        });
    </script>

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

