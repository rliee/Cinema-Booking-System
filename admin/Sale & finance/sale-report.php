<?php
include 'sale_db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link href="finance.css" rel="stylesheet">
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sale-report.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/chart.js"></script>
    <script src="js/chartjs-plugin-datalabels.min.js"></script>
</head>

<body>
    <div class="container">
        <h1 class="chart-heading">
            <i class="fas fa-chart-line"></i>
            Sales Report
        </h1>
        <h1></h1>

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
                        <span class="card-badge">Daily</span>
                    </div>
                    <h2 class="card-number">₱<?= number_format($dailySales, 2); ?></h2>
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
                    <h2 class="card-number">₱<?= number_format($weeklySales, 2); ?></h2>
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
                    <h2 class="card-number">₱<?= number_format($monthlySales, 2); ?></h2>
                </div>
            </div>
        </div>

        <div class="row mt-3 g-3">
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
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5 mb-3">
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
                            <canvas id="monthlyChart"></canvas>
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
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['sales_date']); ?></td>
                                            <td><?= htmlspecialchars($row['movie_name']); ?></td>
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
                            <canvas id="movieChart"></canvas>
                        </div>
                        <div class="ticket-bottom">
                            <?php
                            $colors = ["#1565C0", "#2E7D32", "#F4C430", "#D4AF37", "#E53935", "#B71C1C", "#6A1B9A","#00897B"];
                            $totalRevenue = array_sum($movieData);
                            foreach ($movieLabels as $i => $movie):
                                $sales = $movieData[$i];
                                $percent = $totalRevenue > 0 ? round(($sales / $totalRevenue) * 100) : 0;
                            ?>
                                <div class="legend-row" data-index="<?= $i ?>">
                                    <div class="legend-left">
                                        <span class="legend-dot" style="background:<?= $colors[$i % count($colors)] ?>"></span>
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
        const dailyLabels = <?= json_encode($dailyLabels); ?>;
        const dailyData = <?= json_encode($dailyData); ?>;
        const monthLabels = <?= json_encode($monthLabels); ?>;
        const monthData = <?= json_encode($monthData); ?>;
        const movieLabels = <?= json_encode($movieLabels); ?>;
        const movieData = <?= json_encode($movieData); ?>;
    </script>
    <script src="sale-report.js"></script>
</body>
</html>