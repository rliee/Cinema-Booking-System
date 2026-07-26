<?php
session_start();

require_once __DIR__ . "/includes/db.php";

// Handle Refund Confirmation POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_refund') {
  $booking_id = intval($_POST['booking_id']);
  $user_id = intval($_POST['user_id']);
  $reason = $conn->real_escape_string($_POST['refund_reason']);

  if ($booking_id > 0 && $user_id > 0 && !empty($reason)) {
    // Update booking status to 'Refunded'
    $updateSql = "UPDATE bookings SET booking_status = 'Refunded' WHERE booking_id = $booking_id AND user_id = $user_id";
    if ($conn->query($updateSql) === TRUE) {
      // Insert into refund history
      $insertSql = "INSERT INTO refund_history (booking_id, user_id, reason) VALUES ($booking_id, $user_id, '$reason')";
      $conn->query($insertSql);
      $_SESSION['message'] = "Refund request submitted successfully. The booking has been moved to history.";
    }
  }
  header("Location: my-bookings.php");
  exit();
}

// Dummy/Current Logged-in User ID
$current_user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 1;

// Fetch Active Bookings
$activeSql = "SELECT * FROM bookings WHERE user_id = $current_user_id AND booking_status = 'Active' ORDER BY booking_date DESC";
$activeResult = $conn->query($activeSql);

// Fetch History (Refunded/Completed) Bookings
$historySql = "SELECT b.*, r.reason, r.refunded_at 
              FROM bookings b 
              LEFT JOIN refund_history r ON b.booking_id = r.booking_id 
              WHERE b.user_id = $current_user_id AND b.booking_status = 'Refunded' 
              ORDER BY b.booking_date DESC";
$historyResult = $conn->query($historySql);

// Helper function para sa Poster image path base sa movie title
function getMoviePoster($title)
{
  $title = strtolower(trim($title));
  if (strpos($title, 'avengers') !== false) return 'assets/images/poster/image1.jpg';
  if (strpos($title, 'home') !== false) return 'assets/images/poster/image2.jpg';
  if (strpos($title, 'love you long time') !== false) return 'assets/images/poster/image3.jpg';
  if (strpos($title, 'sputnik') !== false) return 'assets/images/poster/image4.jpg';
  if (strpos($title, 'jurassic') !== false) return 'assets/images/poster/image5.jpg';
  if (strpos($title, 'sheep') !== false) return 'assets/images/poster/image6.jpg';
  if (strpos($title, 'f1') !== false) return 'assets/images/poster/image7.jpg';
  if (strpos($title, 'fantastic') !== false) return 'assets/images/poster/image8.jpg';
  return 'assets/images/poster/image1.jpg'; // default fallback
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Purchases - Cinema Royale</title>

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

  <style>
    body {
      background-color: #0b0b0b;
      color: #ffffff;
      font-family: "Plus Jakarta Sans", "Poppins", Arial, sans-serif;
      padding-top: 100px;
    }

    .page-header {
      padding: 20px 0 15px;
      border-bottom: 1px solid #222;
      margin-bottom: 20px;
    }

    /* Grid Container para magkasya ang 2-3 cards sa isang row */
    .bookings-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
      gap: 16px;
    }

    /* Compact Card Container */
    .booking-card {
      display: flex;
      flex-direction: row;
      background: #181818;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      padding: 12px;
      gap: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
      transition:
        transform 0.2s,
        border-color 0.2s;
    }

    .booking-card:hover {
      border-color: #ffc700;
    }

    /* Mas maliit at eksaktong sukat ng Poster */
    .booking-poster {
      width: 125px;
      height: 185px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      flex-shrink: 0;
    }

    /* Main Details Column */
    .booking-details-content {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      flex-grow: 1;
    }

    .movie-title-text {
      color: #ffc700;
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 8px;
      line-height: 1.2;
    }

    /* Compact 3-Column Metadata Grid */
    .booking-meta-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px 6px;
      margin-bottom: 8px;
    }

    .info-label {
      color: #aaa;
      font-size: 0.7rem;
      font-weight: 600;
      margin-bottom: 2px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .info-label i {
      color: #ffc700;
      font-size: 0.75rem;
    }

    .info-value {
      font-size: 0.8rem;
      font-weight: 600;
      color: #e0e0e0;
      line-height: 1.1;
    }

    /* Action Section sa Ibaba */
    .booking-card-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: auto;
      padding-top: 6px;
    }

    .badge-status {
      background-color: #10b981;
      color: #fff;
      font-size: 0.7rem;
      padding: 4px 12px;
      border-radius: 16px;
      font-weight: 600;
    }

    .badge-status.refunded {
      background-color: #dc3545;
    }

    .btn-refund {
      background-color: transparent;
      border: 1px solid #dc3545;
      color: #dc3545;
      padding: 4px 12px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 0.75rem;
      transition: all 0.2s;
    }

    .btn-refund:hover {
      background-color: #dc3545;
      color: #fff;
    }

    /* Tabs & Modals */
    .nav-pills .nav-link {
      color: #aaa;
      border-radius: 20px;
      padding: 6px 16px;
      font-size: 0.85rem;
    }

    .nav-pills .nav-link.active {
      background-color: #ffc700;
      color: #000;
      font-weight: bold;
    }

    @media (max-width: 480px) {
      .bookings-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>

  <!-- Navbar Header -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: #0b0b0b;">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="logo/Logo.png" alt="Cinema Royale Logo" class="navbar-logo me-2" style="height: 2.5rem; width: auto;" />
        <div>
          <span class="fs-2 p-0 m-0">Cinema Royale</span>
          <div class="navbar-brand-subtitle ms-1">PREMIUM EXPERIENCE</div>
        </div>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav mx-auto">
          <div class="d-flex w-100 justify-content-center text-center">
            <li class="nav-item"><a class="nav-link" href="index.php#hero">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php#now-showing">Now Showing</a></li>
          </div>
          <div class="d-flex w-100 justify-content-center text-center">
            <li class="nav-item"><a class="nav-link" href="index.php#promotions">Promotions</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php#experience">About</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
          </div>
        </ul>
        <div class="auth-buttons ms-auto d-flex flex-lg-row align-items-center justify-content-center my-2">
          <a href="index.php" class="auth-btn login-btn">Home</a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        echo $_SESSION['message'];
        unset($_SESSION['message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <div class="page-header d-flex justify-content-between align-items-center">
      <h2>My Purchases</h2>
      <ul class="nav nav-pills" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="pills-bookings-tab" data-bs-toggle="pill" data-bs-target="#pills-bookings" type="button" role="tab">Active Bookings</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="pills-history-tab" data-bs-toggle="pill" data-bs-target="#pills-history" type="button" role="tab">View History</button>
        </li>
      </ul>
    </div>

    <div class="tab-content" id="pills-tabContent">
      <!-- Active Bookings Tab -->
      <!-- Active Bookings Tab -->
      <div class="tab-pane fade show active" id="pills-bookings" role="tabpanel">
        <?php if ($activeResult && $activeResult->num_rows > 0): ?>
          <div class="bookings-grid">
            <?php while ($row = $activeResult->fetch_assoc()): ?>
              <div class="booking-card">
                <img src="<?php echo getMoviePoster($row['movie_title']); ?>" alt="Poster" class="booking-poster">

                <div class="booking-details-content">
                  <div>
                    <div class="movie-title-text"><?php echo htmlspecialchars($row['movie_title']); ?></div>

                    <div class="booking-meta-grid">
                      <div>
                        <div class="info-label"><i class="fa-solid fa-ticket"></i> Hall</div>
                        <div class="info-value"><?php echo htmlspecialchars($row['cinema_hall']); ?></div>
                      </div>
                      <div>
                        <div class="info-label"><i class="fa-regular fa-calendar-days"></i> Date</div>
                        <div class="info-value"><?php echo htmlspecialchars($row['show_date']); ?></div>
                      </div>
                      <div>
                        <div class="info-label"><i class="fa-regular fa-clock"></i> Time</div>
                        <div class="info-value text-warning"><?php echo htmlspecialchars($row['show_time']); ?></div>
                      </div>
                      <div>
                        <div class="info-label"><i class="fa-solid fa-chair"></i> Seats</div>
                        <div class="info-value"><?php echo htmlspecialchars($row['seats']); ?></div>
                      </div>
                      <div>
                        <div class="info-label"><i class="fa-solid fa-money-bill"></i> Price</div>
                        <div class="info-value text-success">₱<?php echo number_format($row['total_price'], 2); ?></div>
                      </div>
                    </div>
                  </div>

                  <div class="booking-card-actions">
                    <span class="badge-status">Active</span>
                    <button class="btn-refund" data-bs-toggle="modal" data-bs-target="#refundModal"
                      data-bookingid="<?php echo $row['booking_id']; ?>"
                      data-movietitle="<?php echo htmlspecialchars($row['movie_title']); ?>">
                      <i class="fa-solid fa-rotate-left me-1"></i> Refund
                    </button>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-5">
            <h4 class="text-muted">No active bookings found.</h4>
          </div>
        <?php endif; ?>
      </div>

      <!-- History Tab -->
      <!-- History Tab -->
      <div class="tab-pane fade" id="pills-history" role="tabpanel">
        <?php if ($historyResult && $historyResult->num_rows > 0): ?>
          <div class="bookings-grid">
            <?php while ($hRow = $historyResult->fetch_assoc()): ?>
              <div class="booking-card" style="opacity: 0.85;">
                <!-- Poster sa Kaliwa -->
                <img src="<?php echo getMoviePoster($hRow['movie_title']); ?>" alt="Poster" class="booking-poster">

                <!-- Details sa Kanan -->
                <div class="booking-details-content">
                  <div>
                    <div class="movie-title-text text-white"><?php echo htmlspecialchars($hRow['movie_title']); ?></div>

                    <div class="booking-meta-grid">
                      <div>
                        <div class="info-label"><i class="fa-solid fa-ticket"></i> Hall</div>
                        <div class="info-value"><?php echo htmlspecialchars($hRow['cinema_hall']); ?></div>
                      </div>
                      <div>
                        <div class="info-label"><i class="fa-regular fa-calendar-days"></i> Date</div>
                        <div class="info-value"><?php echo htmlspecialchars($hRow['show_date']); ?></div>
                      </div>
                      <div>
                        <div class="info-label"><i class="fa-regular fa-clock"></i> Time</div>
                        <div class="info-value"><?php echo htmlspecialchars($hRow['show_time']); ?></div>
                      </div>
                      <div style="grid-column: span 2;">
                        <div class="info-label"><i class="fa-solid fa-circle-info"></i> Reason</div>
                        <div class="info-value text-light" style="font-size:0.75rem;"><?php echo htmlspecialchars($hRow['reason']); ?></div>
                      </div>
                      <div>
                        <div class="info-label"><i class="fa-solid fa-money-bill"></i> Refunded</div>
                        <div class="info-value text-muted">₱<?php echo number_format($hRow['total_price'], 2); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Footer Status -->
                  <div class="booking-card-actions">
                    <span class="badge-status refunded">Refunded</span>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-5">
            <h4 class="text-muted">No history record found.</h4>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Refund Modal -->
  <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-secondary">
          <h5 class="modal-title text-warning">Request Ticket Refund</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="my-bookings.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="action" value="confirm_refund">
            <input type="hidden" name="booking_id" id="modal-booking-id" value="">
            <input type="hidden" name="user_id" value="<?php echo $current_user_id; ?>">

            <p>Are you sure you want to refund your ticket for <strong id="modal-movie-title" class="text-warning"></strong>?</p>

            <div class="mb-3">
              <label for="refund_reason" class="form-label">Please select a reason for refund:</label>
              <select class="form-select" id="refund_reason" name="refund_reason" required>
                <option value="" disabled selected>-- Select Reason --</option>
                <option value="Change of plans / Schedule conflict">Change of plans / Schedule conflict</option>
                <option value="Accidental booking">Accidental booking</option>
                <option value="Booked wrong date/time">Booked wrong date/time</option>
                <option value="Personal emergency">Personal emergency</option>
                <option value="Others">Others</option>
              </select>
            </div>
          </div>
          <div class="modal-footer border-secondary">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Confirm Refund</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Session & Dynamic Auth Controls Handling
      const loggedIn = localStorage.getItem("loggedIn");
      const userName = localStorage.getItem("userName") || "User";

      if (loggedIn === "true") {
        const authButtons = document.querySelector(".auth-buttons");
        if (authButtons) {
          authButtons.innerHTML = `
            <span class="welcome-text" style="color: #fff; margin-right: 15px; font-weight: 600;">
                Welcome, ${userName}
            </span>
            <a href="my-bookings.php" class="auth-btn login-btn me-2" style="text-decoration: none;">My Bookings</a>
            <a href="#" id="logout-btn" class="auth-btn register-btn" style="text-decoration: none;">Logout</a>
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

      // Refund Modal Data Handler
      const refundModal = document.getElementById('refundModal');
      if (refundModal) {
        refundModal.addEventListener('show.bs.modal', function(event) {
          const button = event.relatedTarget;
          const bookingId = button.getAttribute('data-bookingid');
          const movieTitle = button.getAttribute('data-movietitle');

          document.getElementById('modal-booking-id').value = bookingId;
          document.getElementById('modal-movie-title').textContent = movieTitle;
        });
      }
    });
  </script>
</body>

</html>