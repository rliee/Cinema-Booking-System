<?php
include __DIR__ . '/finance_db.php';
include __DIR__ . '/confirm_booking.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="./finance.css" rel="stylesheet">
    <link href="../../../libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

</head>

<body>
    <div class="container">
        <h1>Finance Dashboard</h1><br>
        <div class="row row-cols-1 row-cols-md-3 row-cols-xl-6 g-4">
            <div class="col">
                <div class="card d-flex justify-content-center">
                    <div class="card-header">Today's Revenue <br></div>
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
                    <div class="card-header">Total Tickets Sold<br></div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totalTickets); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card ">
                    <div class="card-header">Total Transactions<br></div>
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
        <!-- PAYMENT RECORDS -->
        <div class="row">

            <div class="col-lg-8">
                <!-- Recent Payments -->
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
                                                    echo '<span class="badge bg-success">Complete</span>';
                                                } elseif ($status == "pending") {
                                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
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

            <div class="col-lg-4">
                    <!-- Pending Bookings -->
                    <div class="card shadow-sm border-0 mt-4">

                        <!-- Header -->
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">

                                <div>
                                    <h5 class="mb-0">Pending Bookings</h5>

                                    <small class="text-muted">
                                        Pending bookings awaiting confirmation
                                    </small>
                                </div>


                                <span class="badge rounded-pill bg-danger">
                                    <?= $pendingCount; ?>
                                </span>

                            </div>
                        </div>
                        <!-- Pending Booking List -->
                        <div class="card-body">

                            <?php if ($pendingCount > 0) { ?>

                                <?php while ($booking = $pendingBookings->fetch_assoc()) { ?>
                                    <div class="pending-booking-card">
                                        <!-- Customer + Amount -->
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <!-- Customer Name -->
                                                <h6 class="fw-bold mb-1">
                                                    <?= htmlspecialchars($booking['fullname']); ?>
                                                </h6>
                                                <!-- Movie Name -->
                                                <div class="text-muted small">
                                                    <i class="bi bi-film"></i>
                                                    <?= htmlspecialchars($booking['movie_name']); ?>
                                                </div>
                                            </div>
                                            <!-- Amount -->
                                            <strong class="booking-amount">
                                                ₱<?= number_format($booking['total_amount'], 2); ?>
                                            </strong>
                                        </div>
                                        <!-- Ticket Quantity -->
                                        <div class="text-muted small mt-3">
                                            <i class="bi bi-ticket-perforated"></i>
                                            <?= $booking['ticket_qty']; ?> tickets
                                        </div>
                                        <!-- Confirm Button -->
                                        <form action="confirm_booking.php" method="POST">
                                            <input
                                                type="hidden"
                                                name="booking_id"
                                                value="<?= $booking['booking_id']; ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-success w-100 mt-3">
                                                Confirm Booking
                                            </button>   
                                        </form>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <!-- No Pending Booking -->
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle fs-2"></i>
                                    <p class="mt-2 mb-0">
                                        No pending bookings
                                    </p>
                                </div>
                            <?php } ?>
                        </div>
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
                                categoryPercentage: 0.55,
                                barPercentage: 0.75,
                                maxBarThickness: 60,
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
                                        size: 12
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