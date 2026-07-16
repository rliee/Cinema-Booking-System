<?php
include("../includes/db.php");
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Show Scheduling</title>
    <link
        href="../bootstrap-5.3.8-dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="../fontawesome/css/all.min.css"
        rel="stylesheet">
    <link
        rel="stylesheet"
        href="../assets/css/show_scheduling.css">

</head>

<body>
    <div class="container-fluid py-4">

        <!-- ======================================================
         PAGE HEADER
        ======================================================= -->
        <div class="row align-items-center mb-4">

            <!-- left side -->
            <div class="col-lg-8 col-md-7 col-12">
                <h2 class="fw-bold mb-1">
                    <i class="fa-solid fa-film me-2"></i>
                    Show Scheduling
                </h2>
                <p class="text-muted mb-0">
                    Manage movie schedules, cinema halls, and showtimes.
                </p>
            </div>

            <!-- right side -->
            <div class="col-lg-4 col-md-5 col-12 text-md-end mt-3 mt-md-0">
                <button
                    id="btnAddSchedule"
                    class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>
                    Add Schedule
                </button>
            </div>
        </div>

        <!-- dashboard cards -->
        <div class="row g-3 mb-4">

            <!-- total shows -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="dashboard-card h-100">
                    <div class="dashboard-icon bg-primary">
                        <i class="fa-solid fa-clapperboard"></i>
                    </div>

                    <div>
                        <small class="text-muted">Total Shows</small>
                        <h3
                            id="showCount"
                            class="mb-0">
                            0
                        </h3>
                    </div>
                </div>
            </div>

            <!-- tickets sold -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="dashboard-card h-100">
                    <div class="dashboard-icon bg-success">
                        <i class="fa-solid fa-ticket"></i>
                    </div>

                    <div>
                        <small class="text-muted">Tickets Sold</small>
                        <h3
                            id="ticketsSold"
                            class="mb-0">
                            0
                        </h3>
                    </div>
                </div>
            </div>

            <!-- occupancy -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="dashboard-card h-100">
                    <div class="dashboard-icon bg-warning">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div>
                        <small class="text-muted">Occupancy</small>
                        <h3
                            id="occupancyRate"
                            class="mb-0">
                            0%
                        </h3>
                    </div>
                </div>
            </div>

            <!-- revenue -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="dashboard-card h-100">
                    <div class="dashboard-icon bg-danger">
                        <i class="fa-solid fa-peso-sign"></i>
                    </div>

                    <div>
                        <small class="text-muted">Revenue</small>
                        <h3
                            id="totalRevenue"
                            class="mb-0">
                            ₱0.00
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- week selector -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <!-- previous week -->
                    <button
                        id="previousWeek"
                        class="btn btn-outline-secondary">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <!-- week range -->
                    <h5
                        id="weekRange"
                        class="fw-semibold mb-0">
                        Week
                    </h5>

                    <!-- next week -->
                    <button
                        id="nextWeek"
                        class="btn btn-outline-secondary">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <!-- week day cards -->
                <div
                    id="weekContainer"
                    class="week-selector">
                </div>
            </div>
        </div>

        <!-- ======================================================
         SCHEDULE SECTION
    ======================================================= -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1">
                    Scheduled Shows
                </h4>
                <p
                    id="selectedDateText"
                    class="text-muted mb-0">
                    Loading...
                </p>
            </div>
        </div>

        <!-- ======================================================
         SCHEDULE WRAPPER

         JavaScript controls which section is visible.

         loadingState  -> Placeholder Cards

         emptyState    -> No schedules

         scheduleList  -> Actual schedule cards
        ======================================================= -->
        <!-- ======================================================
        SCHEDULE WRAPPER

        This wrapper contains three different UI states.

        1. Loading State
        2. Empty State
        3. Schedule Cards

        JavaScript controls which state is visible.
        ====================================================== -->

        <div class="schedule-wrapper">

            <!-- loading state,
                visible while schedules are being retrieved -->
            <div id="loadingState"></div>

            <!-- ===============================================
         EMPTY STATE

         Displayed when no schedules exist.
    ================================================ -->

            <div
                id="emptyState"
                class="text-center py-5 d-none">

                <i
                    class="fa-solid fa-film text-secondary"
                    style="font-size:80px;">
                </i>

                <h3 class="mt-4">

                    No schedules found

                </h3>

                <p class="text-muted">

                    There are no scheduled movies for the selected day.

                </p>

                <button
                    class="btn btn-primary mt-2"
                    id="btnEmptyAdd">

                    <i class="fa-solid fa-plus me-2"></i>

                    Add Schedule

                </button>

            </div>

            <!-- ===============================================
         ACTUAL SCHEDULE CARDS

         Cards are inserted dynamically using JS.
    ================================================ -->

            <div
                id="scheduleList"
                class="row g-4 d-none">

            </div>

        </div>
    </div>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <!-- ======================================================
     SHOW SCHEDULING JAVASCRIPT
====================================================== -->
<script src="/js/components/ui.js"></script> 
<script src="/js/components/scheduleCard.js"></script> 
<script src="/js/components/skeletonCard.js"></script>   
<script src="/js/show_scheduling.js"></script>

</body>

</html>