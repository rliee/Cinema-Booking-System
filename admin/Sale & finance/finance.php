<?php
include 'finance_db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard</title>
    <link href="finance.css" rel="stylesheet">
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/chart.js"></script>
    <script src="js/chartjs-plugin-datalabels.min.js"></script>
</head>

<body>
    <div class="container">
        <h1 class="chart-heading">
            <i class="fas fa-landmark"></i>
            Finance Dashboard
        </h1>
        <div class="row g-3 mb-4">
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
                    <h2 class="card-number">₱<?= number_format($todayRevenue, 2); ?></h2>
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
                    <h2 class="card-number">₱<?= number_format($weekRevenue, 2); ?></h2>
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
                    <h2 class="card-number">₱<?= number_format($monthRevenue, 2); ?></h2>
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
                    <h2 class="card-number">₱<?= number_format($totalRevenue, 2); ?></h2>
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
                    <h2 class="card-number"><?= number_format($totalTickets); ?></h2>
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
                    <h2 class="card-number"><?= number_format($totalTransactions); ?></h2>
                </div>
            </div>
        </div>
        <!-- CHARTS  -->
        <div class="row mt-3 g-3">
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
                            <canvas id="revenueChart"></canvas>
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
                            <canvas id="ticketChart"></canvas>
                        </div>
                        <div class="ticket-bottom">
                            <?php
                            $colors = [
                                "#1565C0", // Sapphire Blue
                                "#2E7D32", // Emerald Green
                                "#F4C430", // Popcorn Gold
                                "#D4AF37", // Cinema Gold
                                "#E53935", // Crimson
                                "#B71C1C", // Velvet Red
                                "#6A1B9A", // Royal Purple
                                "#00897B"  // Teal
                            ];
                            $totalTicketsChart = array_sum($movieData);
                            foreach ($movieLabels as $i => $movie):
                                $tickets = $movieData[$i];
                                $percent = $totalTicketsChart > 0
                                    ? round(($tickets / $totalTicketsChart) * 100)
                                    : 0;
                            ?>
                                <div class="legend-row" data-index="<?= $i ?>">
                                    <div class="legend-left">
                                        <span
                                            class="legend-dot"
                                            style="background:<?= $colors[$i % count($colors)] ?>">
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
                            <input type="text" id="paymentSearch" placeholder="Search transaction...">
                        </div>
                    </div>
                    <!-- Folder Tabs -->
                    <div id="paymentTabs" class="folder-tabs">
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
                                <tbody id="paymentTable">
                                    <?php while ($row = $payments->fetch_assoc()) { ?>
                                        <tr data-status="<?= $row['payment_status']; ?>">

                                            <td><?= htmlspecialchars($row['transaction_code']); ?></td>
                                            <td>
                                                <Strong><?= htmlspecialchars($row['fullname']); ?></Strong><br>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($row['movie_name']); ?>
                                                </small>
                                            </td>
                                            <td><?= htmlspecialchars($row['payment_method']); ?></td>
                                            <td>₱<?= number_format($row['amount'], 2); ?></td>
                                            <td><?= date('M d, Y', strtotime($row['payment_date'])); ?></td>

                                            <td>

                                                <?php
                                                $status = strtolower($row['payment_status']);

                                                if ($status == "complete") {
                                                    echo '<span class="status-badge complete">
                                                    <i class="fa-solid fa-circle-check"></i> Complete
                                                    </span>';
                                                } elseif ($status == "pending") {
                                                    echo '<span class="status-badge pending">
                                                    <i class="fa-solid fa-clock"></i> Pending
                                                    </span>';
                                                }
                                                ?>

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
        <script>
            const todayRevenue = <?= $todayRevenue ?>;
            const weekRevenue = <?= $weekRevenue ?>;
            const monthRevenue = <?= $monthRevenue ?>;
            const totalRevenue = <?= $totalRevenue ?>;

            const movieLabels = <?= json_encode($movieLabels); ?>;
            const movieData = <?= json_encode($movieData); ?>;
        </script>

        <script src="js/finance.js"></script>
</body>

</html>