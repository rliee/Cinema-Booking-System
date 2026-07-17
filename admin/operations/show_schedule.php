<?php
/* ==========================================================
   SHOW SCHEDULING MODULE

   PURPOSE:
   Allows administrators to manage movie show schedules.

   FEATURES
   - View schedules
   - Create schedule
   - Edit schedule
   - Delete schedule
   - Prevent schedule conflicts
   - Automatic movie end time
   - Automatic turnover time
   - Dashboard statistics

========================================================== */

require_once "../../includes/db.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Show Scheduling</title>
    
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">

   
    <!-- Bootstrap Icons -->
    <link
        href="../fontawesome/css/all.min.css"
        rel="stylesheet">
    <link
        rel="stylesheet"
        href="../assets/css/show_scheduling.css">

</head>

<body>

<!-- ==========================================
     MAIN CONTENT

     NOTE:
     If your ERP already has a sidebar/navbar,
     place this inside your existing content area.
========================================== -->

<div class="container-fluid py-4">

    <!-- ==========================================
         PAGE HEADER
    ========================================== -->

    <div
        class="d-flex
               flex-column
               flex-lg-row
               justify-content-between
               align-items-lg-center
               gap-3
               mb-4">

        <!-- Left Side -->

        <div>

            <h2 class="fw-bold mb-1">

                <i class="fa-solid fa-film me-2"></i>

                Show Scheduling

            </h2>

            <p class="text-muted mb-0">

                Create, manage and monitor
                movie show schedules.

            </p>

        </div>

        <!-- Right Side -->

        <div>

            <button
                class="btn btn-primary"

                id="btnAddSchedule"

                data-bs-toggle="modal"

                data-bs-target="#scheduleModal">

                <i class="fa-solid fa-plus me-2"></i>

                Add Schedule

            </button>

        </div>

    </div>

    <!-- ==========================================
         WEEK SELECTOR
    ========================================== -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div
                class="row
                       align-items-center">

                <!-- Previous Week -->

                <div class="col-auto">

                    <button
                        class="btn btn-outline-secondary"

                        id="previousWeekBtn">

                        <i class="fa-solid fa-chevron-left"></i>

                    </button>

                </div>

                <!-- Days -->

                <div class="col">

                    <div
                        id="weekContainer"

                        class="row
                               row-cols-7
                               g-2">

                        <!--
                            Week cards
                            are generated
                            by weekSelector.js
                        -->

                    </div>

                </div>

                <!-- Next Week -->

                <div class="col-auto">

                    <button
                        class="btn btn-outline-secondary"

                        id="nextWeekBtn">

                        <i class="fa-solid fa-chevron-right"></i>

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- ==========================================
         DASHBOARD STATISTICS

         Part 2 will build these cards.
    ========================================== -->

    <div
        id="statisticsSection"

        class="row g-3 mb-4">

        <!-- Statistics Cards -->

    </div>

    <!-- ==========================================
     SCHEDULE SECTION
========================================== -->

<section id="scheduleSection">

    <!-- ======================================
         SECTION HEADER
    ======================================= -->

    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-3">

        <div>

            <h4 class="fw-bold mb-0">

                Today's Schedule

            </h4>

            <small class="text-muted">

                Movie schedules for the selected day.

            </small>

        </div>

    </div>

    <!-- ======================================
         SCHEDULE CONTAINER

         scheduleCard.js
         skeletonCard.js
         emptyState.js

         all render here.
    ======================================= -->

    <div

        id="scheduleContainer"

        class="row g-4">

    </div>

</section>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script src="../js/components/ui.js"></script>
    <script src="../js/components/scheduleCard.js"></script>
    <script src="../js/components/skeletonCard.js"></script>
    <script src="../js/components/emptyState.js"></script>
    <script src="../js/components/toast.js"></script>
    <script src="../js/components/weekSelector.js"></script>
    <script src="../js/components/statCard.js"></script>
    <script src="../js/request.js"></script>
    <script src="../js/show_schedule.js"></script>
</body>
</html>