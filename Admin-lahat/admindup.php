<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once __DIR__ . "/Includes/connection.php";

// =========================================================
// 0. HANDLE HALL STATUS UPDATE (POST REQUEST)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hall_status'])) {
    $hall_id   = intval($_POST['hall_id']);
    $status_id = intval($_POST['status_id']);

    if ($hall_id > 0 && $status_id > 0) {
        $updateStmt = $conn->prepare("UPDATE cinema_halls SET status_id = ? WHERE hall_id = ? OR id = ?");
        $updateStmt->bind_param("iii", $status_id, $hall_id, $hall_id);

        if ($updateStmt->execute()) {
            // 🔄 Redirect directly back to the Halls section
            header("Location: " . $_SERVER['PHP_SELF'] . "?updated=1&tab=halls");
            exit();
        }
        $updateStmt->close();
    }
}
// =========================================================
// 1. DASHBOARD METRICS QUERIES
// =========================================================
// Today's Revenue
$revResult = $conn->query("SELECT IFNULL(SUM(total_amount),0) AS today_revenue FROM booking_transactions WHERE booking_date = CURDATE() AND booking_status = 'Completed'");
$todayRevenue = ($revResult && $row = $revResult->fetch_assoc()) ? ($row['today_revenue'] ?? 0) : 0;

// Tickets Sold (today, Completed only)
$tixResult = $conn->query("SELECT IFNULL(SUM(total_tickets),0) AS ticket FROM booking_transactions WHERE booking_status = 'Completed' AND booking_date = CURDATE()");
$ticketsSold = ($tixResult && $row = $tixResult->fetch_assoc()) ? ($row['ticket'] ?? 0) : 0;

// Showing Movies
$movResult = $conn->query("SELECT COUNT(*) as movies FROM movies");
$showingMovies = ($movResult && $row = $movResult->fetch_assoc()) ? ($row['movies'] ?? 0) : 0;

// =========================================================
// SCREENINGS TODAY COUNT
// =========================================================
$screenResult = $conn->query("SELECT COUNT(*) as screenings FROM screenings WHERE DATE(start_time) = CURDATE()");
$todayScreenings = ($screenResult && $row = $screenResult->fetch_assoc()) ? ($row['screenings'] ?? 0) : 0;
// Seat Layout Legend
$defaultHallId = 1;

// Efficient query to count Available (1), Occupied (0), and Unavailable (2) seats in one request
$legendQuery = "SELECT 
                    SUM(CASE WHEN seat_status = 1 THEN 1 ELSE 0 END) AS available_count,
                    SUM(CASE WHEN seat_status = 0 THEN 1 ELSE 0 END) AS occupied_count,
                    SUM(CASE WHEN seat_status = 2 THEN 1 ELSE 0 END) AS unavailable_count
                FROM seats 
                WHERE hall_id = ?";

$legendStmt = $conn->prepare($legendQuery);
$legendStmt->bind_param("i", $defaultHallId);
$legendStmt->execute();
$legendResult = $legendStmt->get_result()->fetch_assoc();

// Store counts with safe fallback defaults
$availCount   = $legendResult['available_count']   ?? 0;
$occCount     = $legendResult['occupied_count']    ?? 0;
$unavailCount = $legendResult['unavailable_count'] ?? 0;

$legendStmt->close();

// Fetch all movie data for the counters
$movieCountsQuery = $conn->query("
    SELECT 
        SUM(CASE WHEN LOWER(REPLACE(COALESCE(NULLIF(movie_status, ''), movie_status, ''), ' ', '-')) IN ('now-showing', 'now_showing') THEN 1 ELSE 0 END) as now_showing,
        SUM(CASE WHEN LOWER(REPLACE(COALESCE(NULLIF(movie_status, ''), movie_status, ''), ' ', '-')) IN ('coming-soon', 'coming_soon') THEN 1 ELSE 0 END) as coming_soon,
        SUM(CASE WHEN LOWER(REPLACE(COALESCE(NULLIF(movie_status, ''), movie_status, ''), ' ', '-')) = 'ended' THEN 1 ELSE 0 END) as ended,
        COUNT(*) as total
    FROM movies
");
$movieCounts = ($movieCountsQuery && $row = $movieCountsQuery->fetch_assoc()) ? $row : ['now_showing' => 0, 'coming_soon' => 0, 'ended' => 0, 'total' => 0];

// Fetch counts for all genres
$genreCountsQuery = $conn->query("
    SELECT 
        SUM(CASE WHEN g.genre_name = 'Sci-Fi' THEN 1 ELSE 0 END) as sci_fi,
        SUM(CASE WHEN g.genre_name = 'Drama' THEN 1 ELSE 0 END) as drama,
        SUM(CASE WHEN g.genre_name = 'Action' THEN 1 ELSE 0 END) as action,
        SUM(CASE WHEN g.genre_name = 'Animation' THEN 1 ELSE 0 END) as animation,
        SUM(CASE WHEN g.genre_name = 'Comedy' THEN 1 ELSE 0 END) as comedy,
        SUM(CASE WHEN g.genre_name = 'Horror' THEN 1 ELSE 0 END) as horror
    FROM movies m
    LEFT JOIN genres g ON m.genre_id = g.genre_id
");
$genreCounts = ($genreCountsQuery && $row = $genreCountsQuery->fetch_assoc()) ? $row : [];

// Fetch all movies list
$query = "
    SELECT m.*, g.genre_name 
    FROM movies m
    LEFT JOIN genres g ON m.genre_id = g.genre_id
    ORDER BY m.movie_id DESC
";
$result = $conn->query($query);

// =========================================================
// 2. RECENT TRANSACTIONS QUERY
// =========================================================
$recentTxns = $conn->query("
    SELECT
        customers.customer_name,
        movies.title AS movie_title,
        booking_transactions.total_tickets,
        booking_transactions.total_amount,
        booking_transactions.booking_status
    FROM booking_transactions
    INNER JOIN customers ON booking_transactions.customer_id = customers.customer_id
    INNER JOIN movies ON booking_transactions.movie_id = movies.movie_id
    ORDER BY booking_transactions.transaction_id DESC 
    LIMIT 4
");

// =========================================================
// 3. TODAY'S SCREENINGS QUERY
// =========================================================
$todayShows = $conn->query("
    SELECT movies.title AS movie_title, screenings.start_time AS screening_time
    FROM screenings
    INNER JOIN movies ON screenings.movie_id = movies.movie_id
    WHERE DATE(screenings.start_time) = CURDATE()
    ORDER BY screenings.start_time ASC 
    LIMIT 5
");

// =========================================================
// 4. CINEMA HALLS & METRICS QUERIES
// =========================================================
$hallsResult = $conn->query("
    SELECT h.*, s.status_name 
    FROM cinema_halls h 
    LEFT JOIN hall_statuses s ON h.status_id = s.status_id
");

// Movie & hall lookups for the Show Scheduling "Add Schedule" dropdowns
$scheduleMoviesResult = $conn->query("SELECT movie_id, title, duration FROM movies ORDER BY title");
$scheduleHallsResult  = $conn->query("SELECT hall_id, hall_name FROM cinema_halls ORDER BY hall_name");

// Calculate dynamic metrics for cinema halls overview cards
$hallMetricsQuery = $conn->query("
    SELECT 
        SUM(CASE WHEN LOWER(s.status_name) = 'operational' THEN 1 ELSE 0 END) as active_halls,
        SUM(CASE WHEN LOWER(s.status_name) IN ('maintenance', 'under maintenance') THEN 1 ELSE 0 END) as maintenance_halls,
        SUM(CASE WHEN LOWER(s.status_name) IN ('close', 'closed') THEN 1 ELSE 0 END) as closed_halls,
        SUM(COALESCE(h.total_seats, 0)) as total_capacity
    FROM cinema_halls h
    LEFT JOIN hall_statuses s ON h.status_id = s.status_id
");
$hallMetrics = ($hallMetricsQuery && $row = $hallMetricsQuery->fetch_assoc()) ? $row : [];

$activeHallsCount      = $hallMetrics['active_halls'] ?? 0;
$totalCapacityCount    = $hallMetrics['total_capacity'] ?? 0;
$maintenanceHallsCount = $hallMetrics['maintenance_halls'] ?? 0;
$closedHallsCount      = $hallMetrics['closed_halls'] ?? 0;

// =========================================================
// SALES TRANSACTIONS QUERY
// =========================================================
$salesTxnQuery = "
    SELECT
        booking_transactions.transaction_code,
        customers.customer_name,
        customers.customer_number,
        movies.title AS movie_title,
        booking_transactions.total_amount,
        booking_transactions.booking_date,
        booking_transactions.booking_status,
        booking_transactions.seats,
        booking_transactions.total_tickets
    FROM booking_transactions
    INNER JOIN customers
        ON booking_transactions.customer_id = customers.customer_id
    INNER JOIN movies
        ON booking_transactions.movie_id = movies.movie_id
    ORDER BY booking_transactions.booking_date DESC
";
$salesTxnResult = $conn->query($salesTxnQuery);

// =========================================================
// 5. FINANCE DASHBOARD QUERIES (merged from Finance_Accounting/finance_db.php)
//    Reuses the existing $conn from Includes/connection.php
// =========================================================
// Today's Revenue
$financeTodayRevenue = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total FROM payments WHERE payment_status='Completed' AND DATE(payment_date)=CURDATE()")->fetch_assoc()['total'];
// Week Revenue
$financeWeekRevenue = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total FROM payments WHERE payment_status='Completed' AND YEARWEEK(payment_date)=YEARWEEK(CURDATE())")->fetch_assoc()['total'];
// Month Revenue
$financeMonthRevenue = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total FROM payments WHERE payment_status='Completed' AND MONTH(payment_date)=MONTH(CURDATE()) AND YEAR(payment_date)=YEAR(CURDATE())")->fetch_assoc()['total'];
// Total Revenue
$financeTotalRevenue = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total FROM payments WHERE payment_status='Completed'")->fetch_assoc()['total'];
// Ticket Sales
$financeTotalTickets = $conn->query("SELECT IFNULL(SUM(ticket_qty),0) total FROM bookings")->fetch_assoc()['total'];
// Transactions
$financeTotalTransactions = $conn->query("SELECT COUNT(*) total FROM payments")->fetch_assoc()['total'];
// Donut chart values
$financeMovieQuery = $conn->query("SELECT m.title, SUM(b.ticket_qty) AS total FROM bookings b INNER JOIN movies m ON b.movie_id = m.movie_id GROUP BY m.title");
// Payment records
$financePayments = $conn->query("
    SELECT
        p.payment_id,
        p.booking_id,
        p.reference_number,
        p.payment_method,
        p.amount_paid,
        p.payment_status,
        p.payment_date,
        c.customer_name,
        m.title
    FROM payments p
    INNER JOIN bookings b ON p.booking_id = b.booking_id
    INNER JOIN customers c ON b.customer_id = c.customer_id
    INNER JOIN movies m ON b.movie_id = m.movie_id
    ORDER BY p.payment_date DESC
    LIMIT 25
");
// Bar chart values
$financeBarLabels = ['Today', 'Week', 'Month', 'Total'];
$financeBarData = [
    (float)$financeTodayRevenue,
    (float)$financeWeekRevenue,
    (float)$financeMonthRevenue,
    (float)$financeTotalRevenue
];

$financeMovieLabels = [];
$financeMovieData = [];
while ($row = $financeMovieQuery->fetch_assoc()) {
    $financeMovieLabels[] = $row['title'];
    $financeMovieData[] = (int)$row['total'];
}

// Pending bookings count (Finance widget)
$financePendingCount = 0;
$financePendingQuery = "
    SELECT
        b.booking_id,
        c.customer_name,
        m.title,
        b.ticket_qty,
        b.total_amount
    FROM bookings b
    INNER JOIN customers c ON b.customer_id = c.customer_id
    INNER JOIN movies m ON b.movie_id = m.movie_id
    WHERE b.booking_status = 'Pending'
    ORDER BY b.booking_id DESC
    LIMIT 4
";
$financePendingBookings = $conn->query($financePendingQuery);
if ($financePendingBookings) {
    $financePendingCount = $financePendingBookings->num_rows;
}

// =========================================================
// 6. SALES REPORT QUERIES (merged from Sales_Management/sale-report_dp.php)
//    Reuses the existing $conn from Includes/connection.php
// =========================================================
$reportDailySales = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total_sales FROM payments WHERE payment_status='Completed' AND DATE(payment_date)=CURDATE()")->fetch_assoc()['total_sales'];
$reportWeeklySales = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total_sales FROM payments WHERE payment_status='Completed' AND YEARWEEK(payment_date)=YEARWEEK(CURDATE())")->fetch_assoc()['total_sales'];
$reportMonthlySales = $conn->query("SELECT IFNULL(SUM(amount_paid),0) total_sales FROM payments WHERE payment_status='Completed' AND MONTH(payment_date)=MONTH(CURDATE()) AND YEAR(payment_date)=YEAR(CURDATE())")->fetch_assoc()['total_sales'];

$reportDailySalesQuery = $conn->query("
    SELECT DATE(payment_date) AS sales_date, SUM(amount_paid) AS total_sales
    FROM payments
    WHERE payment_status='Completed'
    GROUP BY DATE(payment_date)
    ORDER BY sales_date DESC
");
$reportWeeklySalesQuery = $conn->query("
    SELECT YEAR(payment_date) AS year, WEEK(payment_date) AS week, SUM(amount_paid) AS total_sales
    FROM payments
    WHERE payment_status='Completed'
    GROUP BY YEAR(payment_date), WEEK(payment_date)
    ORDER BY year DESC, week DESC
");
$reportMonthlySalesQuery = $conn->query("
    SELECT
    DATE(p.payment_date) AS sales_date,
    m.title,
    SUM(b.ticket_qty) AS tickets,
    SUM(p.amount_paid) AS total_sales
FROM payments p
INNER JOIN bookings b ON p.booking_id=b.booking_id
INNER JOIN movies m ON b.movie_id=m.movie_id
WHERE p.payment_status='Completed'
GROUP BY DATE(p.payment_date), m.title 
ORDER BY sales_date DESC LIMIT 25
");
$reportMovieSalesQuery = $conn->query("
    SELECT m.title, SUM(p.amount_paid) AS total_sales, SUM(b.ticket_qty) AS tickets_sold
    FROM payments p
    INNER JOIN bookings b ON p.booking_id=b.booking_id
    INNER JOIN movies m ON b.movie_id=m.movie_id
    WHERE p.payment_status='Completed'
    GROUP BY m.title
    ORDER BY total_sales DESC
");

$reportDailyLabels = [];
$reportDailyData = [];
while ($row = $reportDailySalesQuery->fetch_assoc()) {
    $reportDailyLabels[] = $row['sales_date'];
    $reportDailyData[] = $row['total_sales'];
}

// Dedicated monthly-total query for the Monthly Sales bar chart
// (reportMonthlySalesQuery above is grouped by date+movie, not by month,
// and has no 'month' column — reusing it here produced blank/null labels)
$reportMonthlyChartQuery = $conn->query("
    SELECT
        DATE_FORMAT(MIN(payment_date), '%b %Y') AS month,
        SUM(amount_paid) AS total_sales
    FROM payments
    WHERE payment_status='Complete'
    GROUP BY YEAR(payment_date), MONTH(payment_date)
    ORDER BY YEAR(payment_date) ASC, MONTH(payment_date) ASC
");
$reportMonthLabels = [];
$reportMonthData = [];
while ($row = $reportMonthlyChartQuery->fetch_assoc()) {
    $reportMonthLabels[] = $row['month'];
    $reportMonthData[] = $row['total_sales'];
}

$reportMovieLabels = [];
$reportMovieData = [];
$reportMovieTicketData = [];
while ($row = $reportMovieSalesQuery->fetch_assoc()) {
    $reportMovieLabels[] = $row['title'];
    $reportMovieData[] = $row['total_sales'];
    $reportMovieTicketData[] = (int)$row['tickets_sold'];
}

$reportResult = $conn->query("
    SELECT
        DATE(p.payment_date) AS sales_date,
        m.title,
        SUM(b.ticket_qty) AS tickets,
        SUM(p.amount_paid) AS total_sales
    FROM payments p
    INNER JOIN bookings b ON p.booking_id=b.booking_id
    INNER JOIN movies m ON b.movie_id=m.movie_id
    WHERE p.payment_status='Completed'
    GROUP BY DATE(p.payment_date), m.title
    ORDER BY sales_date DESC
    LIMIT 25
");
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Cinema Booking Admin</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="Base/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Base/base.css">
    <link rel="stylesheet" href="Base/component.css">
    <link rel="stylesheet" href="Operations/Movie_Management/movie-manager.css">
    <link rel="stylesheet" href="Base/modals.css">
    <link rel="stylesheet" href="Dashboard/dashboard1.css">
    <link rel="stylesheet" href="Operations/Seat_Layout/seat-layout.css">
    <link rel="stylesheet" href="Sales_Management/Transaction/customer-transaction.css">
    <link rel="stylesheet" href="Operations/Schedule/show_schedule.css">
    <link rel="stylesheet" href="Operations/Ticket_Price/ticket_pricing.css">
    <link rel="stylesheet" href="Operations/Cinema_Halls/halls.css">
    <link rel="stylesheet" href="Finance_Accounting/finance.css">
    <link rel="stylesheet" href="Sales_Management/Sales_Report/sale-report.css">
</head>

<body>
    <input type="checkbox" id="menu-toggle" class="sidebar-checkbox">
    <header class="bg page-header">
        <label for="menu-toggle" class="open-btn" aria-label="Open menu">☰</label>
    </header>

    <main>
        <!-- ========================================== -->
        <!-- DASHBOARD SECTION -->
        <!-- ========================================== -->
        <div id="page-dashboard" class="page-section">
            <div class="mb-4">
                <h2 class="text-main fw-bold mb-2" style="color: #ffc107;">
                    <i class="fa-solid fa-gauge-high page-header-icon"></i>
                    Dashboard
                </h2>
            </div>
            <div class="dashboard-shell">
                <div class="cards mb-3">
                    <div class="card p-3">
                        <h3>Today's Revenue</h3>
                        <h1 id="revenue">₱<?= number_format($todayRevenue, 2) ?></h1>
                        <p class="card-caption">Completed bookings today</p>
                    </div>
                    <div class="card p-3">
                        <h3>Today's Tickets Sold</h3>
                        <h1 id="tickets"><?= $ticketsSold ?></h1>
                        <p class="card-caption">Completed bookings today</p>
                    </div>
                    <div class="card p-3">
                        <h3>Showing Movies</h3>
                        <h1 id="movies"><?= $showingMovies ?></h1>
                    </div>
                    <div class="card p-3">
                        <h3>Today's Screening</h3>
                        <h1 id="screenings"><?= $todayScreenings ?></h1>
                    </div>
                </div>

                <div class="bottom">
                    <div class="transactions">
                        <h2>Recent Transactions</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Movie</th>
                                    <th>Tickets</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="transactionTable">
                                <?php
                                if ($recentTxns && $recentTxns->num_rows > 0) {
                                    while ($txn = $recentTxns->fetch_assoc()) {
                                        $rtStatus = $txn['booking_status'] ?? 'Pending';
                                        $rtStatusLower = strtolower($rtStatus);
                                        $rtBadgeClass = $rtStatusLower === 'completed' ? 'paid' : ($rtStatusLower === 'cancelled' ? 'cancelled' : 'pending');
                                        $rtAmountFormatted = number_format($txn['total_amount'] ?? 0, 2);
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($txn['customer_name'] ?? 'Unknown') . "</td>";
                                        echo "<td>" . htmlspecialchars($txn['movie_title'] ?? 'Unknown') . "</td>";
                                        echo "<td>" . htmlspecialchars($txn['total_tickets'] ?? '0') . "</td>";
                                        echo "<td>₱" . $rtAmountFormatted . "</td>";
                                        echo "<td><span class=\"txn-status-badge " . $rtBadgeClass . "\">" . htmlspecialchars($rtStatus) . "</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No transactions today</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="screening-list">
                        <h2>Today's Screening</h2>
                        <ul class="screening-ul">
                            <?php
                            if ($todayShows && $todayShows->num_rows > 0) {
                                while ($show = $todayShows->fetch_assoc()) {
                                    $time = date("h:i A", strtotime($show['screening_time']));
                                    echo "<li>🎬 " . htmlspecialchars($show['movie_title']) . " - " . $time . "</li>";
                                }
                            } else {
                                echo "<li>No screenings scheduled for today.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- CUSTOMER TRANSACTION VIEW -->
        <!-- ========================================== -->
        <div id="page-sales" class="page-section d-none">
            <div class="container_custom_transac">
                <div class="mb-3">
                    <h2 class="text-main fw-bold mb-2">
                        <i class="fa-solid fa-receipt page-header-icon"></i>
                        Customer Transactions
                    </h2>
                </div>

                <!-- Tab navigation for filtering completed, pending, and weekly revenue views -->
                <div class="tab-nav" role="tablist" aria-label="Transaction tabs">
                    <button type="button" class="tab-btn active" data-tab="completed">Completed</button>
                    <button type="button" class="tab-btn" data-tab="pending">Pending</button>
                    <button type="button" class="tab-btn" data-tab="cancelled">Cancelled</button>
                    <button type="button" class="tab-btn" data-tab="weekly">Weekly Revenue</button>
                </div>

                <!-- Controls for search, date filtering, and booking status selection -->
                <div class="controls" id="salesTableControls">
                    <input type="text" id="salesSearch" placeholder="Search Transaction...">

                    <input type="date" id="salesDateFilter">

                    <select id="salesStatusFilter">
                        <option value="">All Statuses</option>
                        <option value="Completed">Completed</option>
                        <option value="Pending">Pending</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Weekly Revenue Panel -->
                <div class="weekly-panel_custom_transac" id="salesWeeklyPanel" style="display:none;">
                    <div class="weekly-heading">
                        <div>
                            <h2>Weekly Movie Revenue</h2>
                            <p class="weekly-total">Total Monday &ndash; Sunday: <strong id="salesWeeklyTotal">₱0</strong></p>
                        </div>
                        <span class="weekly-caption">Bar chart view</span>
                    </div>
                    <div class="weekly-chart" id="salesWeeklyDays"></div>
                </div>

                <!-- Transaction Table -->
                <div class="table-scroll-wrapper">
                    <table id="salesTransactionTable">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Customer</th>
                                <th>Customer Number</th>
                                <th>Movie</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Booking Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($salesTxnResult && $salesTxnResult->num_rows > 0): ?>
                                <?php while ($row = $salesTxnResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['transaction_code']); ?></td>
                                        <td><?= htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?= htmlspecialchars($row['customer_number']); ?></td>
                                        <td><?= htmlspecialchars($row['movie_title']); ?></td>
                                        <td data-amount="<?= (float) $row['total_amount']; ?>" data-tickets="<?= (int) $row['total_tickets']; ?>">
                                            ₱<?= number_format($row['total_amount'], 2); ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['booking_date']); ?></td>
                                        <?php
                                        $statusLower = strtolower($row['booking_status']);
                                        $badgeClass = $statusLower === 'completed' ? 'paid' : ($statusLower === 'cancelled' ? 'cancelled' : 'pending');
                                        ?>
                                        <td>
                                            <span class="txn-status-badge <?= $badgeClass; ?>">
                                                <?= htmlspecialchars($row['booking_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button onclick="salesDetails(
                                            '<?= htmlspecialchars($row['transaction_code']); ?>',
                                            '<?= htmlspecialchars($row['customer_name']); ?>',
                                            '<?= htmlspecialchars($row['customer_number']); ?>',
                                            '<?= htmlspecialchars($row['movie_title']); ?>',
                                            '₱<?= number_format($row['total_amount'], 2); ?>',
                                            '<?= htmlspecialchars($row['seats']); ?>',
                                            '<?= htmlspecialchars($row['total_tickets']); ?> Tickets',
                                            '<?= htmlspecialchars($row['booking_status']); ?>'
                                            )">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No transactions found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SALES REPORT SECTION (Sales_Management/sale-report.php) -->
        <!-- ========================================== -->
        <div id="page-reports" class="page-section d-none sales-report-page">
            <div class="container-fluid px-0">
                <div class="chart-heading mb-3">
                    <h2 class="text-main fw-bold mb-2">
                        <i class="fas fa-chart-line page-header-icon"></i>
                        Sales Report
                    </h2>
                </div>


                <div class="row g-3 mb-2">
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-peso-sign"></i>
                                    </div>
                                    <span class="card-label">Today's Revenue</span>
                                </div>
                                <span class="card-badge">Daily</span>
                            </div>
                            <h2 class="card-number">₱<?= number_format($reportDailySales, 2); ?></h2>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-calendar-week"></i>
                                    </div>
                                    <span class="card-label">This Week</span>
                                </div>
                                <span class="card-badge">Weekly</span>
                            </div>
                            <h2 class="card-number">₱<?= number_format($reportWeeklySales, 2); ?></h2>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-chart-column"></i>
                                    </div>
                                    <span class="card-label">This Month</span>
                                </div>
                                <span class="card-badge">Monthly</span>
                            </div>
                            <h2 class="card-number">₱<?= number_format($reportMonthlySales, 2); ?></h2>
                        </div>
                    </div>
                </div>

                <div class="row mt-2 g-3">
                    <div class="col-xl-8 mb-3">
                        <div class="card revenue-card h-100">
                            <div class="card-header chart-header">
                                <div>
                                    <span class="chart-subtitle">Revenue Analytics</span>
                                    <h4 class="chart-heading">
                                        <i class="fa-solid fa-chart-line"></i>
                                        Revenue Overview
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="reportDailyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-5">
                        <div class="card revenue-card h-100">
                            <div class="card-header chart-header">
                                <div>
                                    <span class="chart-subtitle">Monthly Performance</span>
                                    <h4 class="chart-heading">
                                        <i class="fa-solid fa-chart-column"></i>
                                        Monthly Sales
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container bar-chart-container">
                                    <canvas id="reportMonthlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-lg-8">
                        <div class="card h-100 modern-card">
                            <div class="card-header payment-header">
                                <div>
                                    <span class="section-subtitle">Sales Records</span>
                                    <h4 class="section-title">
                                        <i class="fa-solid fa-receipt"></i>
                                        Monthly Breakdown
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table payment-table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Movie</th>
                                                <th>Tickets</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $reportResult->fetch_assoc()) { ?>
                                                <tr style="background:none;">
                                                    <td><?= htmlspecialchars($row['sales_date']); ?></td>
                                                    <td><?= htmlspecialchars($row['title']); ?></td>
                                                    <td><?= htmlspecialchars($row['tickets']); ?></td>
                                                    <td>₱<?= number_format($row['total_sales'], 2); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card revenue-card h-70">
                            <div class="card-header chart-header">
                                <div>
                                    <span class="chart-subtitle">Movie Performance</span>
                                    <h4 class="chart-heading">
                                        <i class="fa-solid fa-chart-pie"></i>
                                        Sales by Movie
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body ticket-body">
                                <div class="ticket-top">
                                    <canvas id="reportMovieChart"></canvas>
                                </div>
                                <div class="ticket-bottom">
                                    <?php
                                    $reportColors = ["#1565C0", "#2E7D32", "#F4C430", "#D4AF37", "#E53935", "#B71C1C", "#6A1B9A", "#00897B"];
                                    $reportMovieTotal = array_sum($reportMovieData);
                                    foreach ($reportMovieLabels as $i => $movie):
                                        $sales = $reportMovieData[$i];
                                        $percent = $reportMovieTotal > 0 ? round(($sales / $reportMovieTotal) * 100) : 0;
                                    ?>
                                        <div class="legend-row" data-index="<?= $i ?>">
                                            <div class="legend-left">
                                                <span class="legend-dot" style="background:<?= $reportColors[$i % count($reportColors)] ?>"></span>
                                                <?= htmlspecialchars($movie) ?>
                                            </div>
                                            <div class="legend-right">
                                                <strong>₱<?= number_format($sales, 2) ?></strong>
                                                <span>(<?= $percent ?>%)</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.reportData = {
                    dailyLabels: <?= json_encode($reportDailyLabels); ?>,
                    dailyData: <?= json_encode($reportDailyData); ?>,
                    monthLabels: <?= json_encode($reportMonthLabels); ?>,
                    monthData: <?= json_encode($reportMonthData); ?>,
                    movieLabels: <?= json_encode($reportMovieLabels); ?>,
                    movieData: <?= json_encode($reportMovieData); ?>
                };
            </script>
        </div>

        <!-- ========================================== -->
        <!-- FINANCE DASHBOARD SECTION (Finance_Accounting/finance.php) -->
        <!-- ========================================== -->
        <div id="page-finance" class="page-section d-none finance-page">
            <div class="container-fluid px-0">
                <div class="chart-heading mb-3">
                    <h2 class="text-main fw-bold mb-2">
                        <i class="fas fa-landmark page-header-icon"></i>
                        Finance Dashboard
                    </h2>
                </div>

                <div class="row g-3 mb-2">
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-peso-sign"></i>
                                    </div>
                                    <span class="card-label">Today's Revenue</span>
                                </div>
                                <span class="card-badge ">Today</span>
                            </div>
                            <h2 class="card-number">₱<?= number_format($financeTodayRevenue, 2); ?></h2>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-calendar-week"></i>
                                    </div>
                                    <span class="card-label">This Week</span>
                                </div>
                                <span class="card-badge">Weekly</span>
                            </div>
                            <h2 class="card-number">₱<?= number_format($financeWeekRevenue, 2); ?></h2>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-chart-column"></i>
                                    </div>
                                    <span class="card-label">This Month</span>
                                </div>
                                <span class="card-badge">Monthly</span>
                            </div>
                            <h2 class="card-number">₱<?= number_format($financeMonthRevenue, 2); ?></h2>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-wallet"></i>
                                    </div>
                                    <span class="card-label">Total Revenue</span>
                                </div>
                                <span class="card-badge">Total</span>
                            </div>
                            <h2 class="card-number">₱<?= number_format($financeTotalRevenue, 2); ?></h2>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-ticket"></i>
                                    </div>
                                    <span class="card-label">Tickets Sold</span>
                                </div>
                                <span class="card-badge">Tickets</span>
                            </div>
                            <h2 class="card-number"><?= number_format($financeTotalTickets); ?></h2>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="dashboard-card">
                            <div class="card-top">
                                <div class="card-left">
                                    <div class="card-icon gold">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <span class="card-label">Transactions</span>
                                </div>
                                <span class="card-badge">Payments</span>
                            </div>
                            <h2 class="card-number"><?= number_format($financeTotalTransactions); ?></h2>
                        </div>
                    </div>
                </div>
                <!-- CHARTS  -->
                <div class="row mt-2 g-3">
                    <div class="col-xl-7  mb-3">
                        <div class="card revenue-card h-70">
                            <div class="card-header chart-header">
                                <div>
                                    <span class="chart-subtitle">
                                        Financial Analytics
                                    </span>
                                    <h4 class="chart-heading">
                                        <i class="fa-solid fa-chart-line"></i>
                                        Revenue Overview
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="financeRevenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-5 mb-3">
                        <div class="bdr card revenue-card h-100">
                            <div class="card-header chart-header">
                                <div>
                                    <span class="chart-subtitle">
                                        Movie Performance
                                    </span>
                                    <h4 class="chart-heading">
                                        <i class="fa-solid fa-chart-pie"></i>
                                        Ticket Sales
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body ticket-body">
                                <div class="ticket-top">
                                    <canvas id="financeTicketChart"></canvas>
                                </div>
                                <div class="ticket-bottom">
                                    <?php
                                    $financeColors = [
                                        "#1565C0", // Sapphire Blue
                                        "#2E7D32", // Emerald Green
                                        "#F4C430", // Popcorn Gold
                                        "#D4AF37", // Cinema Gold
                                        "#E53935", // Crimson
                                        "#B71C1C", // Velvet Red
                                        "#6A1B9A", // Royal Purple
                                        "#00897B"  // Teal
                                    ];
                                    $financeMovieTotal = array_sum($financeMovieData);
                                    foreach ($financeMovieLabels as $i => $movie):
                                        $tickets = $financeMovieData[$i];
                                        $percent = $financeMovieTotal > 0
                                            ? round(($tickets / $financeMovieTotal) * 100)
                                            : 0;
                                    ?>
                                        <div class="legend-row" data-index="<?= $i ?>">
                                            <div class="legend-left">
                                                <span
                                                    class="legend-dot"
                                                    style="background:<?= $financeColors[$i % count($financeColors)] ?>">
                                                </span>
                                                <?= htmlspecialchars($movie) ?>
                                            </div>
                                            <div class="legend-right">
                                                <strong><?= $tickets ?></strong>
                                                Tickets
                                                <span>(<?= $percent ?>%)</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3"></div>
                <!-- PAYMENT RECORDS -->
                <div class="row g-3 mt-2 payment-records-row">
                    <div class="col-lg-12 transaction-column">
                        <!-- Recent Payments -->
                        <div class="card h-100 modern-card w-100">
                            <div class="card-header payment-header">
                                <div>
                                    <span class="section-subtitle">
                                        Transaction History
                                    </span>
                                    <h4 class="section-title">
                                        <i class="fa-solid fa-credit-card"></i>
                                        Payment Records
                                    </h4>
                                </div>
                                <div class="payment-search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="text" id="financePaymentSearch" placeholder="Search transaction...">
                                </div>
                            </div>
                            <!-- Folder Tabs -->
                            <div id="financePaymentTabs" class="folder-tabs">
                                <button class="tab-btn active" data-filter="all">
                                    <i class="fa-solid fa-layer-group"></i>
                                    All
                                </button>
                                <button class="tab-btn" data-filter="Complete">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Complete
                                </button>
                                <button class="tab-btn" data-filter="Pending">
                                    <i class="fa-solid fa-clock"></i>
                                    Pending
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table payment-table table-hover align-middle mb-0">
                                        <thead class="table">
                                            <tr>
                                                <th>Transaction ID</th>
                                                <th>Customer / Movie</th>
                                                <th>Payment Method</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <!-- payment table -->
                                        <tbody id="financePaymentTable">
                                            <?php while ($row = $financePayments->fetch_assoc()) { ?>
                                                <?php
                                                $status = strtolower(trim($row['payment_status'] ?? ''));
                                                if (in_array($status, ['complete', 'completed', 'success', 'paid'], true)) {
                                                    $statusLabel = 'Complete';
                                                } elseif (in_array($status, ['pending', 'processing', 'unpaid'], true)) {
                                                    $statusLabel = 'Pending';
                                                } else {
                                                    $statusLabel = ucwords($row['payment_status'] ?? 'Unknown');
                                                }
                                                ?>
                                                <tr data-status="<?= htmlspecialchars($statusLabel); ?>">

                                                    <td><?= htmlspecialchars($row['reference_number']); ?></td>
                                                    <td>
                                                        <Strong><?= htmlspecialchars($row['customer_name']); ?></Strong><br>
                                                        <small class="text-muted">
                                                            <?= htmlspecialchars($row['title']); ?>
                                                        </small>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['payment_method']); ?></td>
                                                    <td>₱<?= number_format($row['amount_paid'], 2); ?></td>
                                                    <td><?= date('M d, Y', strtotime($row['payment_date'])); ?></td>

                                                    <td>
                                                        <?php if ($statusLabel === 'Complete'): ?>
                                                            <span class="status-badge complete"><i class="fa-solid fa-circle-check"></i> Complete</span>
                                                        <?php elseif ($statusLabel === 'Pending'): ?>
                                                            <span class="status-badge pending"><i class="fa-solid fa-clock"></i> Pending</span>
                                                        <?php else: ?>
                                                            <span class="status-badge"><?= htmlspecialchars($statusLabel); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.financeData = {
                    todayRevenue: <?= $financeTodayRevenue ?>,
                    weekRevenue: <?= $financeWeekRevenue ?>,
                    monthRevenue: <?= $financeMonthRevenue ?>,
                    totalRevenue: <?= $financeTotalRevenue ?>,
                    movieLabels: <?= json_encode($financeMovieLabels); ?>,
                    movieData: <?= json_encode($financeMovieData); ?>
                };
            </script>
        </div>

        <!-- ========================================== -->
        <!-- MOVIE MANAGEMENT SECTION -->
        <!-- ========================================== -->
        <div id="page-operations" class="page-section d-none">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3 mb-4">
                <div>
                    <h2 class="text-main fw-bold mb-2" style="color: #ffc107;">
                        <i class="fa-solid fa-clapperboard page-header-icon"></i>
                        Movie Management
                    </h2>
                </div>
                <button type="button" class="add-btn text-black" data-bs-toggle="modal" data-bs-target="#addMovieModal">
                    + Add Movie
                </button>
            </div>

            <!-- Dynamic Movie Counters -->
            <div class="row g-3">
                <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                    <div class="bg-cinema-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                        <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 30px; background-color: rgba(255, 193, 7, 0.15); border-radius: 8px; padding-left: 8px;">
                            <i class="fa-solid fa-film" style="font-size: 12px; "></i>
                        </div>
                        <div id="count-now-showing" class="fs-5 text-white"><?= $movieCounts['now_showing'] ?? 0 ?></div>
                        <div class="text-secondary small">Now Showing</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                    <div class="bg-cinema-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                        <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 30px; background-color: rgba(255, 193, 7, 0.15); border-radius: 8px; padding-left: 8px;">
                            <i class="fa-solid fa-calendar" style="font-size: 12px; "></i>
                        </div>
                        <div id="count-coming-soon" class="fs-5 fw-bold text-white"><?= $movieCounts['coming_soon'] ?? 0 ?></div>
                        <div class="text-secondary small">Coming Soon</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                    <div class="bg-cinema-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                        <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 31px; height: 30px; background-color: #ff676727; border-radius: 8px; padding-left: 7px;">
                            <i class="fa-solid fa-xmark" style="font-size: 12px; color: #df1414;"></i>
                        </div>
                        <div id="count-ended-runs" class="fs-5 text-white"><?= $movieCounts['ended'] ?? 0 ?></div>
                        <div class="text-secondary small">Ended Runs</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                    <div class="bg-cinema-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                        <div class="mb-1 d-flex align-items-center justify-content-center" style="width: 34px; height: 30px; background-color: #ff676727; border-radius: 8px; padding-left: 8px;">
                            <i class="fa-solid fa-video" style="font-size: 12px; color: #df1414;"></i>
                        </div>
                        <div id="count-total-movies" class="fs-5 text-white"><?= $movieCounts['total'] ?? 0 ?></div>
                        <div class="text-secondary small">Total Movies</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="controls-row">
                <div class="search-container">
                    <i class="fa fa-search search-icon"></i>
                    <input class="control-element" id="movieSearchInput" placeholder="Search movies or directors..." type="text" value="">
                </div>
                <div class="genre-container">
                    <select class="control-element cursor-pointer" id="genreFilterSelect">
                        <option value="All">All Genres</option>
                        <option value="Action">Action</option>
                        <option value="Sci-Fi">Sci-Fi</option>
                        <option value="Animation">Animation</option>
                        <option value="Comedy">Comedy</option>
                        <option value="Horror">Horror</option>
                        <option value="Drama">Drama</option>
                    </select>
                </div>
                <div class="status-container">
                    <select class="control-element cursor-pointer" id="statusFilterSelect">
                        <option value="all">All Status</option>
                        <option value="now_showing">Now Showing</option>
                        <option value="coming_soon">Coming Soon</option>
                        <option value="ended">Ended</option>
                    </select>
                </div>
            </div>

            <p id="no-movies-fallback" class="text-white mt-3" style="display: none;">
                No movies found matching your search.
            </p>

            <div class="dashboard-layout">
                <div class="movie-cards-container" id="movieCardsContainer">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($movie = $result->fetch_assoc()):
                            // Extract movie data
                            $movieId   = $movie['movie_id'] ?? $movie['id'];
                            $genreId   = $movie['genre_id'] ?? '';
                            $genreName = $movie['genre_name'] ?? 'Uncategorized';

                            // Check status dynamically across schema variations
                            $rawStatus = !empty($movie['status'])
                                ? $movie['status']
                                : (!empty($movie['movie_status']) ? $movie['movie_status'] : 'Now Showing');

                            // Format class and text label
                            $statusClean = trim($rawStatus);
                            $statusClass = strtolower(str_replace(' ', '-', $statusClean));
                            $statusLabel = ucwords(strtolower(str_replace('-', ' ', $statusClean)));

                            $rawPoster = trim($movie['poster_url'] ?? '');
                            $posterUrl = htmlspecialchars($rawPoster, ENT_QUOTES);
                            $hasPoster = $rawPoster !== '';
                        ?>
                            <div class="movie-card-horizontal mt-2"
                                data-id="<?php echo $movieId; ?>"
                                data-genre-id="<?php echo $genreId; ?>">
                                <!-- Movie Poster -->
                                <div class="poster-container">
                                    <?php if ($hasPoster): ?>
                                        <img src="../<?php echo $posterUrl; ?>"
                                            alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                            class="movie-poster"
                                            onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="movie-poster-placeholder" style="display:none; align-items:center; justify-content:center; height:100%; background:#1a1a1a; color:#666;">
                                            <i class="fas fa-film"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="movie-poster-placeholder" style="display:flex; align-items:center; justify-content:center; height:100%; background:#1a1a1a; color:#666;">
                                            <i class="fas fa-film"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                </div>

                                <!-- Movie Details -->
                                <div class="movie-details">
                                    <div class="details-header">
                                        <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                                        <div class="meta-tags">
                                            <span class="tag genre"><?php echo htmlspecialchars($genreName); ?></span>
                                            <span class="tag duration"><?php echo htmlspecialchars($movie['duration']); ?> mins</span>
                                            <span class="tag age-rating"><?php echo htmlspecialchars($movie['age_rating'] ?? 'PG-13'); ?></span>
                                        </div>
                                    </div>

                                    <p class="movie-synopsis">
                                        <?php echo htmlspecialchars($movie['synopsis'] ?? 'No synopsis available.'); ?>
                                    </p>

                                    <div class="card-actions">
                                        <a href="<?php echo htmlspecialchars($movie['trailer_url'] ?? '#'); ?>" target="_blank" class="trailer-link">
                                            <i class="fas fa-play"></i> Watch Trailer
                                        </a>
                                        <div class="button-group">
                                            <button type="button"
                                                class="edit-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editMovieModal"
                                                data-id="<?php echo $movieId; ?>"
                                                data-title="<?php echo htmlspecialchars($movie['title'], ENT_QUOTES); ?>"
                                                data-genre-id="<?php echo $genreId; ?>"
                                                data-status="<?php echo htmlspecialchars($statusLabel, ENT_QUOTES); ?>"
                                                data-duration="<?php echo $movie['duration']; ?>"
                                                data-rating="<?php echo htmlspecialchars($movie['age_rating'] ?? '', ENT_QUOTES); ?>"
                                                data-synopsis="<?php echo htmlspecialchars($movie['synopsis'] ?? '', ENT_QUOTES); ?>"
                                                data-poster="<?php echo $hasPoster ? '../' . $posterUrl : ''; ?>">
                                                Edit
                                            </button>
                                            <button type="button" class="delete-btn" data-id="<?php echo $movieId; ?>">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-white mt-3">No movies found in database.</p>
                    <?php endif; ?>
                </div>

                <!-- Genre Distribution Aside Column -->
                <aside class="genre-distribution-aside">
                    <div class="movie-number">
                        <h1 class="header">Genre Distribution</h1>
                        <div class="distribution-wrapper">
                            <div class="bar" data-genre="sci-fi">
                                <div class="mov-name">
                                    <span class="genre">Sci-Fi</span>
                                    <span class="count"><?= $genreCounts['sci_fi'] ?? 0 ?> Films</span>
                                </div>
                                <div class="progress-bar"><span class="number-bar"></span></div>
                            </div>
                            <div class="bar" data-genre="drama">
                                <div class="mov-name">
                                    <span class="genre">Drama</span>
                                    <span class="count"><?= $genreCounts['drama'] ?? 0 ?> Films</span>
                                </div>
                                <div class="progress-bar"><span class="number-bar"></span></div>
                            </div>
                            <div class="bar" data-genre="action">
                                <div class="mov-name">
                                    <span class="genre">Action</span>
                                    <span class="count"><?= $genreCounts['action'] ?? 0 ?> Films</span>
                                </div>
                                <div class="progress-bar"><span class="number-bar"></span></div>
                            </div>
                            <div class="bar" data-genre="animation">
                                <div class="mov-name">
                                    <span class="genre">Animation</span>
                                    <span class="count"><?= $genreCounts['animation'] ?? 0 ?> Films</span>
                                </div>
                                <div class="progress-bar"><span class="number-bar"></span></div>
                            </div>
                            <div class="bar" data-genre="comedy">
                                <div class="mov-name">
                                    <span class="genre">Comedy</span>
                                    <span class="count"><?= $genreCounts['comedy'] ?? 0 ?> Films</span>
                                </div>
                                <div class="progress-bar"><span class="number-bar"></span></div>
                            </div>
                            <div class="bar" data-genre="horror">
                                <div class="mov-name">
                                    <span class="genre">Horror</span>
                                    <span class="count"><?= $genreCounts['horror'] ?? 0 ?> Films</span>
                                </div>
                                <div class="progress-bar"><span class="number-bar"></span></div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
        <!-- SHOW SCHEDULING SECTION-->
        <div id="page-schedule" class="page-section d-none">

            <!-- ===== PAGE HEADER ===== -->
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    <h2 class="fw-bold mb-2 text-main">
                        <i class="fa-solid fa-film page-header-icon"></i>
                        Show Scheduling
                    </h2>
                </div>
                <div>
                    <button class="btn btn-warning" id="btnAddSchedule" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                        <i class="fa-solid fa-plus me-2" style="color:black;"></i>
                        Add Schedule
                    </button>
                </div>
            </div>

            <!-- ===== WEEK SELECTOR ===== -->
            <div class="card card-hover-none shadow-sm border-0 mb-4">
                <div class="card-body p-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="week-wrapper">
                                <div class="week-navigation">
                                    <button type="button" class="week-btn" onclick="previousWeek()">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                    <h4 id="weekTitle" class="week-title mb-0"></h4>
                                    <button type="button" class="week-btn" onclick="nextWeek()">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                                <div id="weekContainer" class="row g-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== DASHBOARD STATISTICS ===== -->
            <div id="statisticsSection" class="row g-3 mb-4"></div>

            <!-- ===== SCHEDULE SECTION ===== -->
            <section id="scheduleSection">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-0 text-main" id="scheduleText">Today's Schedule</h4>
                        <small class="text-white small" id="scheduleDescription">Movie schedules for the selected day.</small>
                    </div>
                    <div class="schedule-search-wrapper mb-4" style="max-width: 30rem; min-width: 30%">
                        <div class="input-group schedule-search">
                            <span class="input-group-text">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" class="form-control" id="scheduleSearch" placeholder="Search schedules by movie title">
                        </div>
                    </div>
                </div>

                <div id="scheduleContainer" class="row g-4"></div>

                <!-- ===== ADD / EDIT SCHEDULE MODAL ===== -->
                <div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content bg-dark text-white border-0">
                            <form id="addScheduleForm">
                                <input type="hidden" id="scheduleId" name="schedule_id">
                                <div class="modal-header border-secondary">
                                    <h4 class="modal-title" id="scheduleModalTitle">Add Schedule</h4>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <!-- Movie (Add Mode) -->
                                        <div class="col-md-6" id="movieSelectWrapper">
                                            <label for="movie" class="form-label">Movie</label>
                                            <select class="form-select" id="movie" name="movie_id" required>
                                                <option value="">Select Movie</option>
                                                <?php
                                                if ($scheduleMoviesResult) {
                                                    while ($movie = $scheduleMoviesResult->fetch_assoc()) :
                                                ?>
                                                        <option value="<?= $movie['movie_id'] ?>" data-duration="<?= $movie['duration'] ?>">
                                                            <?= htmlspecialchars($movie['title']) ?>
                                                        </option>
                                                <?php
                                                    endwhile;
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Movie (Edit Mode) -->
                                        <div class="col-md-6 d-none" id="movieDisplayWrapper">
                                            <label class="form-label">Movie</label>
                                            <input type="text" id="movieDisplay" class="form-control" readonly>
                                        </div>

                                        <!-- hall -->
                                        <div class="col-md-6">
                                            <label for="hall_id" class="form-label">Cinema Hall</label>
                                            <select class="form-select" id="hall" name="hall_id" required>
                                                <option value="">Select Hall</option>
                                                <?php
                                                if ($scheduleHallsResult) {
                                                    while ($hall = $scheduleHallsResult->fetch_assoc()) :
                                                ?>
                                                        <option value="<?= $hall['hall_id'] ?>">
                                                            <?= htmlspecialchars($hall['hall_name']) ?>
                                                        </option>
                                                <?php
                                                    endwhile;
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- date -->
                                        <div class="col-md-4">
                                            <label for="show_date" class="form-label">Show Date</label>
                                            <input type="date" class="form-control" id="showDate" name="show_date" required>
                                        </div>

                                        <!-- start time -->
                                        <div class="col-md-4">
                                            <label for="start_time" class="form-label">Start Time</label>
                                            <input type="time" class="form-control" id="startTime" name="start_time" required>
                                        </div>

                                        <!-- end time -->
                                        <div class="col-md-4">
                                            <label for="endTime" class="form-label">End Time</label>
                                            <input type="time" class="form-control" id="endTime" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-secondary">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>
                                        Save Schedule
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- CINEMA HALLS SECTION -->
        <div id="page-halls" class="page-section d-none">
            <div class="mb-3">
                <h2 class="text-main fw-bold mb-2" style="color: #ffc107;">
                    <i class="fa-solid fa-door-open page-header-icon"></i>
                    Cinema Halls
                </h2>
            </div>
            <div class="row g-2 mb-3">
                <!-- Card 1: Active Halls -->
                <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                    <div class="bg-hall-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                        <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 30px; background-color: #ffc10726; border-radius: 8px; padding-left: 9px;">
                            <i class="fa-solid fa-warehouse" style="font-size: 12px;"></i>
                        </div>
                        <div class="fs-5 text-white fw-bold" id="metricActiveHalls"><?php echo $activeHallsCount; ?></div>
                        <div class="text-secondary small">Active Halls</div>
                    </div>
                </div>

                <!-- Card 2: Total Capacity -->
                <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                    <div class="bg-hall-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                        <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 30px; background-color: rgba(255, 193, 7, 0.15); border-radius: 8px; padding-left: 9px;">
                            <i class="fa-solid fa-user-group" style="font-size: 12px;"></i>
                        </div>
                        <div class="fs-5 fw-bold text-white" id="metricTotalCapacity"><?php echo $totalCapacityCount; ?></div>
                        <div class="text-secondary small">Total Capacity</div>
                    </div>
                </div>

                <!-- Card 3: Under Maintenance -->
                <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                    <div class="bg-hall-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                        <div class="mb-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #ff67672a; border-radius: 8px; padding-left: 8px;">
                            <i class="fa-solid fa-screwdriver-wrench" style="font-size: 12px; color: #e40707;"></i>
                        </div>
                        <div class="fs-5 text-white fw-bold" id="metricMaintenanceHalls"><?php echo $maintenanceHallsCount; ?></div>
                        <div class="text-secondary small">Under Maintenance</div>
                    </div>
                </div>

                <!-- Card 4: Closed Halls -->
                <div class="col-6 col-md-3 col-sm-6 col-lg-3">
                    <div class="bg-hall-card p-3" style="background-color: #1a1a1a; border-color: #151515; border-radius: 18px;">
                        <div class="mb-1 d-flex align-items-center justify-content-center" style="width: 29px; height: 32px; background-color: #ff67673d; border-radius: 8px; padding-left: 7px;">
                            <i class="fa-solid fa-xmark" style="font-size: 12px; color: #df1414;"></i>
                        </div>
                        <div class="fs-5 text-white fw-bold" id="metricClosedHalls"><?php echo $closedHallsCount; ?></div>
                        <div class="text-secondary small">Closed Hall</div>
                    </div>
                </div>
            </div>

            <!-- Grid Wrapper Container for Hall Cards -->
            <div class="halls-grid-container">
                <?php
                if ($hallsResult && $hallsResult->num_rows > 0) {
                    while ($hall = $hallsResult->fetch_assoc()) {
                        $hallId       = $hall['id'] ?? $hall['hall_id'] ?? 1;
                        $hallName     = htmlspecialchars($hall['name'] ?? $hall['hall_name'] ?? 'Cinema Hall');
                        $hallCapacity = htmlspecialchars($hall['total_seats'] ?? 0);
                        $rawStatus    = $hall['status_name'] ?? $hall['status_id'] ?? 'operational';
                        $hallStatus   = strtolower($rawStatus);

                        // Map database status string to CSS class styling
                        $statusClass = 'operational';
                        if ($hallStatus === 'maintenance' || $hallStatus === 'under maintenance') {
                            $statusClass = 'maintenance';
                        } elseif ($hallStatus === 'closed' || $hallStatus === 'close') {
                            $statusClass = 'closed';
                        }
                ?>
                        <div class="hall-card hall-card-item" data-hall-id="hall-<?= $hallId ?>">
                            <span class="hall-status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucwords($hallStatus)) ?></span>
                            <div class="card-header">
                                <div class="title-area">
                                    <div class="icon-container"><i class="fas fa-warehouse" style="background: #ffc10726;"></i></div>
                                    <div>
                                        <h3 class="card-title"><?= $hallName ?></h3>
                                        <span class="card-subtitle">hall-<?= $hallId ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="specs-grid">
                                <div class="spec-box">
                                    <span class="spec-label">Capacity</span>
                                    <span class="spec-value spec-capacity"><?= $hallCapacity ?></span>
                                </div>
                                <div class="spec-box">
                                    <span class="spec-label">Seats Occupied</span>
                                    <span class="spec-value spec-occupied">0</span>
                                </div>
                                <div class="spec-box">
                                    <span class="spec-label">Unavailable</span>
                                    <span class="spec-value spec-unavailable">0</span>
                                </div>
                            </div>
                            <div class="actions-container">
                                <button class="btn-secondary" data-bs-toggle="modal" data-bs-target="#viewSeatsModal" data-hall-id="<?= $hallId ?>">View Seats</button>
                                <button class="btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editHallStatusModal"
                                    data-hall-id="<?= $hallId ?>"
                                    data-hall-name="<?= $hallName ?>"
                                    data-status-id="<?= $hall['status_id'] ?? 1 ?>">
                                    Edit Hall
                                </button>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p class='text-white'>No cinema halls found in the database.</p>";
                }
                ?>
            </div>
        </div>
        <!-- SEAT LAYOUTS SECTION -->
        <div id="page-seats" class="page-section d-none">
            <!-- Top Component Header Grid -->
            <div class="d-flex justify-content-between align-items-start mb-4 gap-3">
                <div>
                    <h2 class="text-main fw-bold mb-2" style="color: #ffc107;">
                        <i class="fa-solid fa-chair page-header-icon"></i>
                        Seat Layout Editor
                    </h2>
                </div>
                <div class="mt-2 text-end">
                    <button id="editLayoutBtn" class="sle-btn-action-edit" type="button">
                        <svg class="sle-icon-pencil-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                        Edit Layout
                    </button>
                    <div id="sle-lock-notice" class="text-warning small mt-1 d-none">
                        Editing disabled &mdash; this hall is under maintenance or closed.
                    </div>
                </div>
            </div>
            <div class="sle-workspace-layout-wrapper">
                <div class="sle-workspace-column-left">
                    <div class="sle-panel-card-wrapper mb-3">
                        <h3 class="sle-panel-main-title">Select Hall</h3>
                        <div class="sle-hall-list-container">
                            <?php
                            // Rewind the halls result set (already consumed above for the hall cards)
                            if ($hallsResult) {
                                $hallsResult->data_seek(0);
                            }
                            $sleFirst = true;
                            while ($hallsResult && $hall = $hallsResult->fetch_assoc()):
                                $sleHallId    = $hall['id'] ?? $hall['hall_id'] ?? 1;
                                $sleHallName  = $hall['name'] ?? $hall['hall_name'] ?? ('Cinema Hall ' . $sleHallId);
                                $sleStatusId  = intval($hall['status_id'] ?? 1);
                                $sleRawStatus = strtolower($hall['status_name'] ?? 'operational');
                                $sleLocked    = ($sleStatusId === 2 || $sleStatusId === 3);
                            ?>
                                <div class="sle-hall-item-row<?= $sleFirst ? ' sle-status-selected' : '' ?><?= $sleLocked ? ' sle-hall-locked' : '' ?>"
                                    data-hall-id="hall-<?= $sleHallId ?>"
                                    data-status-id="<?= $sleStatusId ?>">
                                    <div class="sle-hall-name-text"><?= htmlspecialchars($sleHallName) ?></div>
                                    <?php if ($sleLocked): ?>
                                        <span class="sle-hall-lock-badge" title="Editing disabled &mdash; hall is <?= htmlspecialchars(ucwords($sleRawStatus)) ?>">&#128274; <?= htmlspecialchars(ucwords($sleRawStatus)) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php $sleFirst = false;
                            endwhile; ?>
                        </div>
                    </div>

                    <!-- Seat Layout Legend Container -->
                    <div class="sle-legend-container">
                        <h4 class="sle-legend-title">Legend</h4>
                        <div class="sle-legend-items-group">
                            <div class="sle-legend-item">
                                <span class="sle-legend-swatch sle-legend-available"></span>
                                <span class="sle-legend-label">
                                    Available <span class="sle-legend-count" id="sle-count-available">(<?= $availCount ?>)</span>
                                </span>
                            </div>
                            <div class="sle-legend-item">
                                <span class="sle-legend-swatch sle-legend-occupied"></span>
                                <span class="sle-legend-label">
                                    Occupied <span class="sle-legend-count" id="sle-count-occupied">(<?= $occCount ?>)</span>
                                </span>
                            </div>
                            <div class="sle-legend-item">
                                <span class="sle-legend-swatch sle-legend-unavailable"></span>
                                <span class="sle-legend-label">
                                    Unavailable <span class="sle-legend-count" id="sle-count-unavailable">(<?= $unavailCount ?>)</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Left Column -->

                <!-- RIGHT COLUMN: Seating Module Component -->
                <div class="sle-workspace-column-right">
                    <div class="sle-seating-card-wrapper animate-fade-in p-4">
                        <div class="screen-container">
                            <h3 id="sle-current-hall-title">Cinema Hall 1</h3>
                            <div class="screen-bar"></div>
                            <div class="screen-text pb-2">SCREEN</div>
                        </div>
                        <div id="sle-dynamic-grid" class="sle-page-grid-container"></div>
                    </div>
                </div> <!-- End Right Column -->
            </div> <!-- End Main Workspace Split Wrapper -->
        </div>
        <!-- END SEAT LAYOUTS SECTION -->

        <!-- TICKET PRICING SECTION -->
        <div id="page-pricing" class="page-section d-none">
            <div>
                <div>
                    <h1 class="page-title text-main" style="color: #ffc107;">
                        <i class="fa-solid fa-tags page-header-icon"></i>
                        Ticket Pricing
                    </h1>
                </div>
            </div>

            <!-- Statistics -->
            <div class="statistics-section">
                <div class="row g-4">
                    <!-- Movies with Pricing -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card card-circle stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-title">Movies with Pricing</div>
                                    <div class="stat-value" id="totalMoviesPricing">0</div>
                                    <div class="stat-subtitle">Active ticket prices</div>
                                </div>
                                <div class="stat-icon"><i class="fa-solid fa-film"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Price -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card card-circle stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-title">Average Price</div>
                                    <div class="stat-value" id="averageTicketPrice">₱0</div>
                                    <div class="stat-subtitle">Current base ticket price</div>
                                </div>
                                <div class="stat-icon"><i class="fa-solid fa-peso-sign"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Discounts -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card card-circle stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-title">Discount Categories</div>
                                    <div class="stat-value" id="totalDiscounts">0</div>
                                    <div class="stat-subtitle">Available discounts</div>
                                </div>
                                <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Highest Discount -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card card-circle stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-title">Highest Discount</div>
                                    <div class="stat-value" id="highestDiscount">0%</div>
                                    <div class="stat-subtitle">Maximum customer discount</div>
                                </div>
                                <div class="stat-icon"><i class="fa-solid fa-percent"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Ticket Prices -->
                <div class="col-12 col-lg-6">
                    <div class="pricing-table-card h-100">
                        <div class="pricing-table-header d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-0 text-main">Ticket Prices</h5>
                                <small class="text-white">Manage movie ticket base prices</small>
                            </div>
                            <button class="btn btn-warning btn-sm" onclick="resetTicketPriceForm()" data-bs-toggle="modal" data-bs-target="#ticketPriceModal">
                                <i class="fa-solid fa-plus" style="color:black;"></i>
                                Add Price
                            </button>
                        </div>
                        <div class="pricing-table-body">
                            <div class="table-responsive">
                                <table class="pricing-table">
                                    <thead>
                                        <tr>
                                            <th>Movie</th>
                                            <th>Ticket Price</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ticketPriceTableBody">
                                        <tr>
                                            <td colspan="3" class="text-center">Loading ticket prices...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discount Categories -->
                <div class="col-12 col-lg-6">
                    <div class="pricing-table-card h-100">
                        <div class="pricing-table-header d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="text-main mb-0">Discount Categories</h5>
                                <small class="text-white">Manage customer discount percentages</small>
                            </div>
                            <button class="btn btn-warning btn-sm" onclick="resetDiscountForm()" data-bs-toggle="modal" data-bs-target="#discountModal">
                                <i class="fa-solid fa-plus" style="color:black;"></i>
                                Add Discount
                            </button>
                        </div>
                        <div class="pricing-table-body">
                            <div class="table-responsive">
                                <table class="pricing-table">
                                    <thead>
                                        <tr>
                                            <th>Discount</th>
                                            <th>Discount Rate %</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="discountTableBody">
                                        <tr>
                                            <td colspan="3" class="text-center">Loading discounts...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add / Edit Ticket Price Modal -->
            <div class="modal fade" id="ticketPriceModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="ticketPriceForm">
                            <div class="modal-header">
                                <h5 class="modal-title d-flex align-items-center gap-2" id="ticketPriceModalTitle">
                                    <i class="fa-solid fa-ticket"></i>
                                    <span>Add Ticket Price</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="priceId" name="price_id">
                                <input type="hidden" id="editMovieId" name="edit_movie_id">

                                <!-- Movie -->
                                <div class="mb-3">
                                    <label class="form-label">Movie</label>
                                    <select class="form-select" id="movieId" name="movie_id" required>
                                        <option value="">Select a movie</option>
                                    </select>
                                    <div id="movieName" class="selected-display" style="display:none;">
                                        <i class="fa-solid fa-film"></i>
                                        <span></span>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="mb-3">
                                    <label class="form-label">Ticket Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="ticketPrice" name="price" min="1" step="0.01" placeholder="Enter ticket price" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="ticketPriceSubmitButton">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add / Edit Discount Modal -->
            <div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="discountForm">
                            <div class="modal-header">
                                <h5 class="modal-title d-flex align-items-center gap-2" id="discountModalTitle">
                                    <i class="fa-solid fa-tags"></i>
                                    <span>Add Discount</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="discountId" name="discount_id">

                                <!-- Discount Name -->
                                <div class="mb-3">
                                    <label class="form-label">Discount Name</label>
                                    <input type="text" class="form-control" id="discountName" name="discount_name" required>
                                    <div id="discountNameDisplay" class="selected-display" style="display:none;">
                                        <i class="fa-solid fa-tags"></i>
                                        <span></span>
                                    </div>
                                </div>

                                <!-- Discount Percentage -->
                                <div class="mb-3">
                                    <label class="form-label">Discount Percentage</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discountPercentage" name="discount_percentage" min="1" max="99" step="1" placeholder="Enter discount percentage" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="discountSubmitButton">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TICKET PRICING SECTION -->

        <!-- SHARED CONFIRMATION MODAL (used by Show Scheduling & Ticket Pricing) -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="confirmationModalTitle">
                            <i id="confirmationModalIcon" class="fa-solid fa-trash"></i>
                            <span>Confirm Action</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0" id="confirmationModalMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmationModalConfirmButton">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SHARED MESSAGE MODAL (used by Show Scheduling & Ticket Pricing) -->
        <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="messageModalTitle">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Message</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0" id="messageModalBody"></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-gold" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast container (Show Scheduling) -->
        <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>
    </main>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="profile">
                <img src="Images/gojo.png" alt="Administrator" class="profile-avatar">
                <div class="profile-info">
                    <h4>Gojo Satoru</h4>
                    <p>Administrator</p>
                </div>
            </div>
            <label for="menu-toggle" class="close-btn">×</label>
        </div>
        <div class="name">
            <h1 class="text-center align-middle my-2">
                <img src="Images/Logo.png" alt="Cinema Royale">
                <span class="link-text">Cinema Royale</span>
            </h1>
        </div>
        <div class="sidebar-content">
            <a href="admin.php" class="mt-4 active" data-page="dashboard"><i class="fa-solid fa-house"></i>
                <span class="link-text">Dashboard</span></a>
            <details class="sidebar-dropdown" data-group="sales">
                <summary class="menu-group" data-group="sales"><i class="fa-solid fa-chart-line"></i> <span class="link-text">Sales Management</span></summary>
                <a href="#" class="menu-link" data-page="sales" data-group="sales"><span class="link-text">Customer Transaction</span></a>
                <a href="#" class="menu-link" data-page="reports" data-group="sales"><span class="link-text">Sales Reports</span></a>
            </details>
            <details class="sidebar-dropdown" data-group="finance">
                <summary class="menu-group" data-group="finance"><i class="fa-solid fa-wallet"></i> <span class="link-text">Finance & Accounting</span></summary>
                <a href="#" class="menu-link" data-page="finance" data-group="finance"><span class="link-text">Financial Dashboard</span></a>
            </details>
            <details class="sidebar-dropdown mb-5" data-group="operations">
                <summary class="menu-group" data-group="operations"><i class="fa-solid fa-gear"></i> <span class="link-text">Operations</span></summary>
                <a href="#" class="menu-link" data-page="operations" data-group="operations"><span class="link-text">Movie Management</span></a>
                <a href="#" class="menu-link" data-page="schedule" data-group="operations"><span class="link-text">Show Scheduling</span></a>
                <a href="#" class="menu-link" data-page="halls" data-group="operations"><span class="link-text">Cinema Halls</span></a>
                <a href="#" class="menu-link" data-page="seats" data-group="operations"><span class="link-text">Seat Layouts</span></a>
                <a href="#" class="menu-link" data-page="pricing" data-group="operations"><span class="link-text">Ticket Pricing</span></a>
            </details>
        </div>
    </div>

    <!-- EDIT MOVIE MODAL -->
    <div class="modal fade" id="editMovieModal" tabindex="-1" aria-labelledby="editMovieModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMovieModalLabel">Edit Movie Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editMovieForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="editMovieId" name="id">

                        <div class="row g-3">
                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div>
                                    <label for="editMovieTitle" class="form-label">Movie Title</label>
                                    <input type="text" class="form-control" id="editMovieTitle" name="title" required placeholder="Enter movie title">
                                </div>
                                <div>
                                    <label for="editMoviePoster" class="form-label">Poster Image</label>
                                    <input type="file" class="form-control" id="editMoviePoster" name="poster" accept="image/*">
                                    <img id="editMoviePosterPreview" src="" alt="Current poster preview" class="mt-2 rounded" style="max-height: 120px; display: none;">
                                </div>
                                <div>
                                    <label for="editMovieGenre" class="form-label">Genre</label>
                                    <select class="form-select" id="editMovieGenre" name="genre_id" required>
                                        <option value="">Select Genre</option>
                                        <option value="1">Action</option>
                                        <option value="2">Sci-Fi</option>
                                        <option value="3">Comedy</option>
                                        <option value="4">Drama</option>
                                        <option value="5">Horror</option>
                                        <option value="6">Animation</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div>
                                    <label for="editMovieStatus" class="form-label">Status</label>
                                    <select class="form-select" id="editMovieStatus" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Now Showing">Now Showing</option>
                                        <option value="Coming Soon">Coming Soon</option>
                                        <option value="Ended">Ended</option>
                                    </select>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="editMovieDuration" class="form-label">Duration (mins)</label>
                                        <input type="number" class="form-control" id="editMovieDuration" name="duration" required placeholder="e.g., 120">
                                    </div>
                                    <div class="col-6">
                                        <label for="editMovieRating" class="form-label">Age Rating</label>
                                        <select class="form-select" id="editMovieRating" name="rating" required>
                                            <option value="" disabled selected>Select Rating</option>
                                            <option value="G">G</option>
                                            <option value="PG">PG</option>
                                            <option value="PG-13">PG-13</option>
                                            <option value="R">R</option>
                                            <option value="NC-17">NC-17</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="editMovieSynopsis" class="form-label">Synopsis</label>
                                    <textarea class="form-control" id="editMovieSynopsis" name="synopsis" rows="3" required placeholder="Write a short summary..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary text-white border-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-black font-weight-bold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE MOVIE MODAL -->
    <div class="modal fade" id="deleteMovieModal" tabindex="-1" aria-labelledby="deleteMovieModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-danger">
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger" id="deleteMovieModalLabel">Delete Movie Confirmation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteMovieTarget" class="text-warning"></strong>?</p>
                    <p class="small text-secondary mb-0">This action cannot be undone and will permanently remove this record.</p>
                </div>
                <div class="modal-footer border-danger">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger" style="font-weight: lighter;">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD MOVIE MODAL -->
    <div class="modal fade" id="addMovieModal" tabindex="-1" aria-labelledby="addMovieModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMovieModalLabel">Add New Movie</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="addMovieForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div>
                                    <label for="addMovieTitle" class="form-label">Movie Title</label>
                                    <input type="text" class="form-control" id="addMovieTitle" name="title" required placeholder="Enter movie title">
                                </div>
                                <div>
                                    <label for="addMoviePoster" class="form-label">Poster Image</label>
                                    <input type="file" class="form-control" id="addMoviePoster" name="poster" accept="image/*">
                                </div>
                                <div>
                                    <label for="addMovieGenre" class="form-label">Genre</label>
                                    <select class="form-select" id="addMovieGenre" name="genre_id" required>
                                        <option value="">Select Genre</option>
                                        <option value="1">Action</option>
                                        <option value="2">Sci-Fi</option>
                                        <option value="3">Comedy</option>
                                        <option value="4">Drama</option>
                                        <option value="5">Horror</option>
                                        <option value="6">Animation</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex flex-column gap-3">
                                <div>
                                    <label for="addMovieStatus" class="form-label">Showing Status</label>
                                    <select class="form-select" id="addMovieStatus" name="status" required>
                                        <option value="" disabled selected>Select status</option>
                                        <option value="Now Showing">Now Showing</option>
                                        <option value="Coming Soon">Coming Soon</option>
                                        <option value="Ended">Ended</option>
                                    </select>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="addMovieDuration" class="form-label">Duration</label>
                                        <input type="text" class="form-control" id="addMovieDuration" name="duration" placeholder="e.g., 120" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="addMovieRating" class="form-label">Age Rating</label>
                                        <select class="form-select" id="addMovieRating" name="rating" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="G">G</option>
                                            <option value="PG">PG</option>
                                            <option value="PG-13">PG-13</option>
                                            <option value="R">R</option>
                                            <option value="NC-17">NC-17</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="addMovieTrailer" class="form-label">Trailer URL</label>
                                    <input type="url" class="form-control" id="addMovieTrailer" name="trailer_url" placeholder="https://www.youtube.com/watch?v=...">
                                </div>
                                <div>
                                    <label for="addMovieSynopsis" class="form-label">Synopsis</label>
                                    <textarea class="form-control" id="addMovieSynopsis" name="synopsis" rows="3" required placeholder="Write a short summary..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary text-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Add Movie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT CINEMA HALL STATUS MODAL -->
    <div class="modal fade" id="editHallStatusModal" tabindex="-1" aria-labelledby="editHallStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px; width: 100%;">
            <div class="modal-content bg-dark text-white border-secondary" style="background-color: #1a1a1a !important; border-color: #333 !important;">

                <div class="modal-header border-secondary" style="border-bottom: 1px solid #333 !important;">
                    <h5 class="modal-title fw-bold text-white" id="editHallStatusModalLabel">
                        Edit Hall Status <span id="editHallTitleName" class="text-warning fs-6 ms-1"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editHallStatusForm" method="POST" action="">
                    <div class="modal-body py-4">
                        <!-- Identifier for PHP POST handler -->
                        <input type="hidden" name="update_hall_status" value="1">

                        <!-- Hidden Hall ID (populated dynamically via JS) -->
                        <input type="hidden" id="editHallId" name="hall_id" value="">

                        <div class="mb-3">
                            <label for="editHallStatusSelect" class="form-label text-secondary small fw-bold">OPERATIONAL STATE</label>
                            <select class="form-select bg-dark text-white border-secondary" id="editHallStatusSelect" name="status_id" style="background-color: #121212 !important; color: #fff !important; border-color: #333 !important;" required>
                                <option value="1" class="bg-dark text-white">Operational</option>
                                <option value="2" class="bg-dark text-white">Under Maintenance</option>
                                <option value="3" class="bg-dark text-white">Closed</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer border-secondary" style="border-top: 1px solid #333 !important;">
                        <button type="button" class="btn btn-outline-secondary text-white border-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning btn-sm px-3 fw-bold text-black">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- VIEW SEATS MODAL -->
    <div class="modal fade" id="viewSeatsModal" tabindex="-1" aria-labelledby="viewSeatsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-cinema-dark text-white border-zinc-800" style="background-color: #121212;">
                <div class="modal-header border-zinc-800">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-1" id="viewSeatsModalLabel">Seat Map Preview</h5>
                        <span class="text-secondary small id-subtitle">CINEMA HALL-1 · Grand Auditorium</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="screen-container mb-2 text-center">
                        <div class="screen-bar" style="height: 4px; background: linear-gradient(to right, transparent, #ffc107, transparent); margin-bottom: 5px;"></div>
                        <span class="screen-text small text-secondary">SCREEN THIS WAY</span>
                    </div>
                    <div id="modal-dynamic-grid" class="unified-page-grid-container"></div>
                    <div class="modal-legend d-flex justify-content-center align-items-center gap-4 py-2">
                        <!-- Available Legend -->
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 14px; height: 14px; background-color: #262626; border-radius: 3px; display: inline-block;"></span>
                            <span class="text-light small">Available (<span id="legend-available-count">144</span>)</span>
                        </div>

                        <!-- Occupied Legend -->
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 14px; height: 14px; background-color: #b91c1c; border-radius: 3px; display: inline-block;"></span>
                            <span class="text-light small">Occupied (<span id="legend-occupied-count">0</span>)</span>
                        </div>

                        <!-- Unavailable Legend -->
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 14px; height: 14px; background-color: #ac4800; border-radius: 3px; display: inline-block;"></span>
                            <span class="text-light small">Unavailable (<span id="legend-unavailable-count">0</span>)</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-zinc-800">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 text-light border-zinc-700" data-bs-dismiss="modal">Close View</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal (moved to a direct child of <body> so its
         position:fixed centers on the real viewport, not a transformed
         ancestor inside <main>) -->
    <div class="transaction-modal" id="salesModal">
        <div class="modal-content">
            <h2>Booking Details</h2>
            <span class="close" onclick="closeSalesModal()">&times;</span>
            <div class="info" id="salesBookingInfo"></div>
            <div class="modal-btn-row">
                <button id="salesCompleteBookingBtn"
                    class="complete-btn"
                    onclick="completeSalesBooking()">
                    Completed
                </button>
                <button id="salesCancelBookingBtn"
                    class="cancel-btn"
                    onclick="cancelSalesBooking()">
                    Cancel Booking
                </button>
            </div>
        </div>
    </div>

    <script src="Base/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="Base/navigation.js"></script>
    <script src="Base/page-router.js"></script>
    <script src="Dashboard/dashboard1.js"></script>
    <script src="Base/metrics.js"></script>
    <script src="Base/filters.js"></script>
    <script src="Operations/Movie_Management/movie-manager.js"></script>
    <script src="Operations/Seat_Layout/seat-layout.js"></script>
    <script src="Sales_Management/Transaction/customer-transaction.js"></script>
    <script src="Includes/chart.js"></script>
    <script src="Includes/chartjs-plugin-datalabels.min.js"></script>
    <script src="Sales_Management/Sales_Report/sale-report.js"></script>
    <script src="Finance_Accounting/finance.js"></script>

    <!-- Shared components (Show Scheduling & Ticket Pricing) -->
    <script src="Operations/components/ui.js"></script>
    <script src="Operations/components/skeletonCard.js"></script>
    <script src="Operations/components/emptyState.js"></script>
    <script src="Operations/components/toast.js"></script>
    <script src="Operations/components/statCard.js"></script>
    <script src="Operations/components/request.js"></script>
    <script src="Operations/components/modals.js"></script>

    <!-- Show Scheduling -->
    <script src="Operations/components/scheduleCard.js"></script>
    <script src="Operations/components/weekSelector.js"></script>
    <script src="Operations/components/show_schedule.js"></script>
    <script src="Operations/components/statCard.js"></script>

    <!-- Ticket Pricing -->
    <script src="Operations/components/ticketPriceTable.js"></script>
    <script src="Operations/components/discountTable.js"></script>
    <script src="Operations/components/movieDropdown.js"></script>
    <script src="Operations/components/ticket_pricing.js"></script>
</body>

</html>