<?php
include("customer-transaction-php/connection.php");

/* Today's Revenue */
$revenueQuery = "
SELECT IFNULL(SUM(total_amount),0) AS today_revenue
FROM booking_transactions
WHERE booking_date = CURDATE()
AND booking_status='Completed'
";
$revenueResult = $conn->query($revenueQuery);
$todayRevenue = $revenueResult->fetch_assoc()['today_revenue'];

/* Total Tickets Sold */
$ticketQuery = "
SELECT IFNULL(SUM(total_tickets),0) AS ticket
FROM booking_transactions
";
$ticketResult = $conn->query($ticketQuery);
$row = $ticketResult->fetch_assoc();
$totalTickets = $row['ticket'];

/* Showing Movies */
$movieQuery = "
SELECT COUNT(*) AS movies
FROM movies
";
$movieResult = $conn->query($movieQuery);
$totalMovies = $movieResult->fetch_assoc()['movies'];

/* Today's Screening */
$screeningQuery = "
SELECT COUNT(*) AS screenings
FROM movie_schedule
WHERE screening_date = CURDATE()
";
$screeningResult = $conn->query($screeningQuery);


/* Recent Transactions */
$recentQuery = "
SELECT
customers.customer_name,
movies.movie_title,
booking_transactions.total_tickets,
booking_transactions.total_amount
FROM booking_transactions
INNER JOIN customers
ON booking_transactions.customer_id = customers.customer_id
INNER JOIN movies
ON booking_transactions.movie_id = movies.movie_id
ORDER BY booking_transactions.transaction_id DESC
LIMIT 5
";

$recentTransactions = $conn->query($recentQuery);

/* Today's Schedule */
$scheduleQuery = "
SELECT
movies.movie_title,
movie_schedule.screening_time
FROM movie_schedule
INNER JOIN movies
ON movie_schedule.movie_id = movies.movie_id
WHERE movie_schedule.screening_date = CURDATE()
ORDER BY screening_time ASC
";
$schedules = $conn->query($scheduleQuery);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Dashboard</title>

    <link rel="stylesheet" href="style.css">
    <link href="dashboard.css" rel="stylesheet">

    <link rel="stylesheet" href="header-footer.css">
    <script src="dashboard.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="dashboard-shell">
        <div class="main">
            <div class="header">
                <h1 style="color:#ffd54a;">Dashboard</h1>
                <div class="profile">
                    Admin
                </div>
            </div>

            <div class="cards">
                <div class="card">
                    <i class="fas fa-coins icon"></i>
                    <h3>Today's Revenue</h3>
                    <h1 id="revenue">
                        ₱<?php echo number_format($todayRevenue) ?>
                    </h1>
                </div>
                <div class="card">
                    <i class="fas fa-ticket-alt icon"></i>
                    <h3>Tickets Sold</h3>
                    <h1 id="tikets"> <?php echo number_format($totalTickets);?> </h1>
                </div>

                <div class="card">
                    <i class="fas fa-film icon"></i>
                    <h3>Showing Movies</h3>
                    <h1 id="movies"><?= $totalMovies ?></h1>
                </div>

                <div class="card">
                    <i class="fas fa-video icon"></i>
                    <h3>Today's Screening</h3>
                    <h1 id="screenings">4</h1>
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
                            </tr>
                        </thead>
                            <tbody id="transactionTable">

                            <?php while($row = $recentTransactions->fetch_assoc()){ ?>

                                <tr>

                                <td><?= htmlspecialchars($row['customer_name']) ?></td>

                                <td><?= htmlspecialchars($row['movie_title']) ?></td>

                                <td><?= $row['total_tickets'] ?></td>

                                <td>₱<?= number_format($row['total_amount'],2) ?></td>

                                </tr>

                            <?php } ?>

                            </tbody>
                    </table>
                </div>

                <div class="schedule">
                    <h2>Today's Screening</h2>
                    <ul>
                       <?php
                            $sql = "SELECT movie_title, screening_time FROM movies";
                            $result = $conn->query($sql);

                            while($row = $result->fetch_assoc()){
                                echo "<li>🎬 "
                                    . htmlspecialchars($row['movie_title'])
                                    . " - "
                                    . date("g:i A", strtotime($row['screening_time']))
                                    . "</li>";
                            }
                            ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>

</html>