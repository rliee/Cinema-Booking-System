<?php
include("../includes/db.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1">
    <title>Show Scheduling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/show_schedule.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- side bar 'to -->
            <?php include("../includes/sidebar.php"); ?>
            <div class="col-md-10 p-4">
                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h1 class="page-title">

                            Show Scheduling

                        </h1>

                        <p class="text-secondary">

                            Manage movie showtimes across all cinema halls.

                        </p>

                    </div>

                    <button
                        class="btn btn-warning"

                        data-bs-toggle="modal"

                        data-bs-target="#addScheduleModal">

                        <i class="bi bi-plus-lg"></i>

                        Add Show

                    </button>

                </div>



                <!-- WEEKLY CALENDAR -->

                <div class="calendar-container">

                    <div class="calendar-header">

                        <button class="btn btn-dark"
                            id="prevWeek">

                            <i class="bi bi-chevron-left"></i>

                        </button>

                        <h4 id="monthYear">

                            July 2026

                        </h4>

                        <button class="btn btn-dark"
                            id="nextWeek">

                            <i class="bi bi-chevron-right"></i>

                        </button>

                    </div>

                    <div
                        id="weekContainer"

                        class="row mt-3">

                    </div>

                </div>



                <!-- STATISTICS -->

                <div class="row mt-4">

                    <div class="col-md-6">

                        <h3>

                            Today's Shows

                            <span id="showCount">

                                0

                            </span>

                        </h3>

                    </div>

                    <div class="col-md-6 text-end">

                        <h5>

                            Tickets Sold

                            <span id="ticketsSold">

                                0

                            </span>

                        </h5>

                    </div>

                </div>



                <!-- SHOW CARDS -->

                <div
                    id="scheduleList"
                    class="mt-3">

                </div>


            </div>

        </div>

    </div>


    <?php include("add_schedule.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/show_schedule.js"></script>

</body>

</html>