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
require_once __DIR__ . "/../../includes/db.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Show Scheduling</title>
    <link rel="stylesheet" href="../../libraries/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../libraries/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../css/show_schedule.css">

</head>

<body>

<!-- ==========================================
     MAIN CONTENT

     NOTE:
     If your ERP already has a sidebar/navbar,
     place this inside your existing content area.
========================================== -->

<div class="container-fluid py-4">

    <!-- ===== PAGE HEADER ===== -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

        <!-- Left Side -->
        <div>
            <h2 class="fw-bold mb-1">
                <i class="fa-solid fa-film me-2"></i>
                Show Scheduling
            </h2>
            <p class="text-muted mb-0">Create, manage and monitor movie show schedules.</p>
        </div>

        <!-- Right Side -->
        <div>
            <button class="btn btn-primary" id="btnAddSchedule" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                <i class="fa-solid fa-plus me-2"></i>
                Add Schedule
            </button>
        </div>
    </div>

    <!-- ===== WEEK SELECTOR ===== -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <!-- Previous Week -->
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" id="previousWeekBtn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                </div>

                <!-- Days -->
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

                <!-- Next Week -->
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" id="nextWeekBtn">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== DASHBOARD STATISTICS ===== -->
    <div id="statisticsSection" class="row g-3 mb-4">
        <!-- Statistics Cards -->
    </div>

    <!-- ===== SCHEDULE SECTION ===== -->
    <section id="scheduleSection">

        <!-- ===== SECTION HEADER ===== -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-0">Today's Schedule</h4>
                <small class="text-muted">Movie schedules for the selected day.</small>
            </div>
        </div>

        <!-- ===== SCHEDULE CONTAINER -->
        <div id="scheduleContainer" class="row g-4"></div>

        <!-- ===== ADD / EDIT SCHEDULE MODAL ===== -->
        <div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-dark text-white border-0">
                    <form id="addScheduleForm">
                        <input type="hidden" id="scheduleId" name="schedule_id">
                        <!-- header -->
                        <div class="modal-header border-secondary">
                            <h4 class="modal-title" id="scheduleModalTitle">Add Schedule</h4>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- body -->
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- movie -->
                                <div class="col-md-6">
                                    <label for="movie_id" class="form-label">Movie</label>
                                    <select class="form-select" id="movie" name="movie_id" required>
                                        <option value="">Select Movie</option>

                                        <?php
                                        $movies = mysqli_query($conn, "SELECT movie_id, title, duration FROM movies ORDER BY title");
                                        while ($movie = mysqli_fetch_assoc($movies)) :
                                        ?>
                                            <option
                                                value="<?= $movie['movie_id'] ?>"
                                                data-duration="<?= $movie['duration'] ?>">

                                                <?= htmlspecialchars($movie['title']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- hall -->
                                <div class="col-md-6">
                                    <label for="hall_id" class="form-label">Cinema Hall</label>
                                    <select class="form-select" id="hall" name="hall_id" required>
                                        <option value="">Select Hall</option>

                                        <?php
                                        $halls = mysqli_query($conn, "SELECT hall_id, hall_name FROM cinema_halls ORDER BY hall_name");
                                        while ($hall = mysqli_fetch_assoc($halls)) :
                                        ?>
                                            <option
                                                value="<?= $hall['hall_id'] ?>">
                                                <?= htmlspecialchars($hall['hall_name']) ?>
                                            </option>
                                        <?php endwhile; ?>
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

                        <!-- footer -->
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Cancel</button>
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

    <!-- toast container -->
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>
    

    <script src="../../libraries/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

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