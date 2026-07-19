<?php
include 'db.php';
// Today's Revenue
$todayRevenue = $conn->query("
SELECT IFNULL(SUM(amount),0) total
FROM payments
WHERE payment_status='Complete'
AND DATE(payment_date)=CURDATE()
")->fetch_assoc()['total'];
// Week Revenue
$weekRevenue = $conn->query("
SELECT IFNULL(SUM(amount),0) total
FROM payments
WHERE payment_status='Complete'
AND YEARWEEK(payment_date)=YEARWEEK(CURDATE())
")->fetch_assoc()['total'];
// Month Revenue
$monthRevenue = $conn->query("
SELECT IFNULL(SUM(amount),0) total
FROM payments
WHERE payment_status='Complete'
AND MONTH(payment_date)=MONTH(CURDATE())
AND YEAR(payment_date)=YEAR(CURDATE())
")->fetch_assoc()['total'];
// Total Revenue
$totalRevenue = $conn->query("
SELECT IFNULL(SUM(amount),0) total
FROM payments
WHERE payment_status='Complete'
")->fetch_assoc()['total'];
// Ticket Sales
$totalTickets = $conn->query("
SELECT IFNULL(SUM(ticket_qty),0) total
FROM bookings
")->fetch_assoc()['total'];
// Transactions
$totalTransactions = $conn->query("
SELECT COUNT(*) total
FROM payments
")->fetch_assoc()['total'];

// DONUT CHART VALUES
$movieQuery = $conn->query("
SELECT
m.movie_name,
SUM(b.ticket_qty) AS total
FROM bookings b
INNER JOIN movies m
ON b.movie_id = m.movie_id
GROUP BY m.movie_name
");

$movieLabels = [];
$movieData = [];

while ($row = $movieQuery->fetch_assoc()) {
    $movieLabels[] = $row['movie_name'];
    $movieData[] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="finance.css" rel="stylesheet">
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

</head>

<body>
    <div class="container">
        <h1>Finance Dashboard</h1><br>
        <div class="row row-cols-1 row-cols-md-3 row-cols-xl-6 g-4">
            <div class="col">
                <div class="card d-flex justify-content-center">
                    <div class="card-header">Today's Revenue</div>
                    <div class="card-body">
                        <h5 class="card-title">₱<?= number_format($todayRevenue, 2); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card ">
                    <div class="card-header">This Week's Revenue</div>
                    <div class="card-body">
                        <h5 class="card-title">₱<?= number_format($weekRevenue, 2); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card ">
                    <div class="card-header">This Month's Revenue</div>
                    <div class="card-body">
                        <h5 class="card-title">₱<?= number_format($monthRevenue, 2); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card ">
                    <div class="card-header">Total Revenue <br></div>
                    <div class="card-body">
                        <h5 class="card-title">₱<?= number_format($totalRevenue, 2); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card ">
                    <div class="card-header">Total Tickets Sold</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totalTickets); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card ">
                    <div class="card-header">Total Transactions</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totalTransactions); ?></h5>
                    </div>
                </div>
            </div>
        </div> <br>
        <!-- CHARTS  -->
        <div class="row mt-4">

            <div class="col-lg-6 mb-4">

                <div class="card revenue-card h-100">

                    <div class="card-header bg-white border-0 pt-4 px-4">

                        <h5 class="fw-bold mb-1">

                            <i class="bi bi-bar-chart-line me-2"></i>

                            Revenue Overview

                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="chart-container">

                            <canvas id="revenueChart"></canvas>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-6 mb-4 ">

                <div class="card revenue-card h-100">

                    <div class="card-header bg-white border-0 pt-4 px-4">

                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-pie-chart me-2"></i>
                            Ticket Sales by Movie
                        </h5>

                    </div>

                    <div class="card-body ticket-body">

                        <div class="ticket-left">
                            <canvas id="ticketChart"></canvas>
                        </div>

                        <div class="ticket-right">

                            <?php
                            $colors = [
                                "#2F80ED",
                                "#27AE60",
                                "#F39C12",
                                "#EB5757",
                                "#9B51E0",
                                "#00B8D9",
                                "#FF6F61"
                            ];

                            $totalTicketsChart = array_sum($movieData);

                            foreach ($movieLabels as $i => $movie):

                                $tickets = $movieData[$i];

                                $percent = $totalTicketsChart > 0
                                    ? round(($tickets / $totalTicketsChart) * 100)
                                    : 0;
                            ?>

                                <div class="legend-row">

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

        </div><br><br>

        <?php
        /* ============================
   PAYMENT RECORDS
=============================*/
        $payments = $conn->query("
SELECT
    p.transaction_code,
    c.fullname,
    m.movie_name,
    p.payment_method,
    p.amount,
    p.payment_date,
    p.payment_status
FROM payments p
INNER JOIN bookings b ON p.booking_id = b.booking_id
INNER JOIN customers c ON b.customer_id = c.customer_id
INNER JOIN movies m ON b.movie_id = m.movie_id
ORDER BY payment_date DESC, payment_id DESC
");

        /* ============================
   BAR CHART VALUES
=============================*/
        $barLabels = ['Today', 'Week', 'Month', 'Total'];
        $barData = [
            (float)$todayRevenue,
            (float)$weekRevenue,
            (float)$monthRevenue,
            (float)$totalRevenue
        ];


        ?>

        <!-- PAYMENT RECORDS -->

        <div class="card shadow-sm border-0 mt-4">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h5 class="mb-0">Payment Records</h5>
                        <small class="text-muted">
                            Latest payment transactions
                        </small>
                    </div>

                </div>

                <!-- Folder Tabs -->

                <ul class="nav nav-tabs mt-3" id="paymentTabs">

                    <li class="nav-item">
                        <button
                            class="nav-link active"
                            data-filter="All">
                            All
                        </button>
                    </li>

                    <li class="nav-item">
                        <button
                            class="nav-link"
                            data-filter="Complete">
                            Complete
                        </button>
                    </li>

                    <li class="nav-item">
                        <button
                            class="nav-link"
                            data-filter="Pending">
                            Pending
                        </button>
                    </li>

                </ul>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>Transaction ID</th>
                                <th>Customer</th>
                                <th>Movie</th>
                                <th>Payment Method</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody id="paymentTable">

                            <?php while ($row = $payments->fetch_assoc()) { ?>

                                <tr data-status="<?= $row['payment_status']; ?>">

                                    <td><?= htmlspecialchars($row['transaction_code']); ?></td>

                                    <td><?= htmlspecialchars($row['fullname']); ?></td>

                                    <td><?= htmlspecialchars($row['movie_name']); ?></td>

                                    <td><?= htmlspecialchars($row['payment_method']); ?></td>

                                    <td>
                                        ₱<?= number_format($row['amount'], 2); ?>
                                    </td>

                                    <td>
                                        <?= date('M d, Y', strtotime($row['payment_date'])); ?>
                                    </td>

                                    <td>

                                        <?php
                                        $status = strtolower($row['payment_status']);

                                        if ($status == "complete") {
                                        ?>
                                            <span class="badge bg-success">Complete</span>

                                        <?php
                                        } elseif ($status == "pending") {
                                        ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php
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

        <script>
            Chart.register(ChartDataLabels);

            const revenueChart = new Chart(
                document.getElementById("revenueChart"), {

                    type: "bar",

                    data: {

                        labels: [
                            "Today",
                            "This Week",
                            "This Month",
                            "Total Revenue"
                        ],

                        datasets: [{

                            data: [
                                <?= $todayRevenue ?>,
                                <?= $weekRevenue ?>,
                                <?= $monthRevenue ?>,
                                <?= $totalRevenue ?>
                            ],

                            backgroundColor: [

                                "#34C759",
                                "#2F80ED",
                                "#F39C12",
                                "#FF3B30"

                            ],
                            categoryPercentage:0.55,
                            barPercentage:0.75,
                            maxBarThickness:60,
                            borderRadius: 12,
                            borderSkipped: false,
                            animation: {
                            animateRotate: true,
                            duration: 1200
                        },

                        }]

                    },

                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        plugins: {

                            legend: {
                                display: false
                            },

                            tooltip: {

                                backgroundColor: "#111827",

                                padding: 12,

                                callbacks: {

                                    label: function(context) {

                                        return "₱" + context.raw.toLocaleString();

                                    }

                                }

                            },

                            datalabels: {

                                anchor: "end",

                                align: "top",

                                color: "#1F2937",

                                font: {

                                    weight: "bold",

                                    size: 14

                                },

                                formatter: function(value) {

                                    return "₱" + value.toLocaleString();

                                }

                            }

                        },

                        layout: {

                            padding: {
                                top: 30
                            }

                        },

                        scales: {

                            x: {

                                grid: {
                                    display: false
                                },

                                ticks: {

                                    color: "#64748B",

                                    font: {
                                        size: 13
                                    }

                                }

                            },

                            y: {

                                beginAtZero: true,

                                grid: {

                                    color: "#EEF2F7",

                                    drawBorder: false

                                },

                                ticks: {

                                    color: "#64748B",

                                    callback: function(value) {

                                        return "₱" + Number(value).toLocaleString();

                                    }

                                }

                            }

                        }

                    }

                });
            const ticketChart = new Chart(document.getElementById('ticketChart'), {

                type: 'doughnut',

                data: {

                    labels: <?= json_encode($movieLabels); ?>,

                    datasets: [{

                        data: <?= json_encode($movieData); ?>,

                        backgroundColor: [
                            "#2F80ED",
                            "#27AE60",
                            "#F39C12",
                            "#EB5757",
                            "#9B51E0",
                            "#00B8D9",
                            "#FF6F61"
                        ],

                        borderColor: "#fff",
                        borderWidth: 5,
                        spacing: 2,
                        hoverOffset: 10,
                        animation: {
                            animateRotate: true,
                            duration: 1200
                        },


                    }]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    cutout: "72%",
                    radius: "92%",

                    plugins: {

                        legend: {
                            display: false
                        }

                    }

                }

            });
        </script>


        <SCRIPT src="finance.js"></SCRIPT>








</body>

</html>