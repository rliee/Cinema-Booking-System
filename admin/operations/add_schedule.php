<div class="modal fade" id="addScheduleModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <form id="addScheduleForm">
                <div class="modal-header">
                    <h4>Add Show Schedule</h4>
                    <button class="btn-close btn-close-white" data-bs-dismiss=""></button>
                </div>
                <div class="modal-body">
                    <div class="row"> <!-- Movie -->
                        <div class="col-md-6 mb-3">
                            <label>Movie</label>
                            <select name="movie_id" id="movie" class="form-select">
                                <option value="">Select Movie</option>
                                <?php
                                $sql = "SELECT * FROM movies WHERE status='Now Showing' ORDER BY title";
                                $result = mysqli_query($conn, $sql);
                                while ($movie = mysqli_fetch_assoc($result)) {
                                    echo "
                                        <option
                                            value='" . $movie['movie_id'] . "'
                                            data-duration='" . $movie['duration'] . "'>
                                            " . $movie['title'] . "
                                        </option>
                                    ";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Cinema Halls Schedule -->
                        <div class="col-md-6 mb-3">
                            <label>Cinema Hall</label>
                            <select name="hall_id" class="form-select">
                                <option>Select Hall</option>

                                <?php
                                $sql = "SELECT * FROM cinema_halls";
                                $result = mysqli_query($conn, $sql);
                                while ($hall = mysqli_fetch_assoc($result)) {
                                    echo "
                                        <option value='" . $hall['hall_id'] . "'>
                                            " . $hall['hall_name'] . "
                                        </option>
                                    ";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="col-md-4">
                            <label>Date</label>
                            <input type="date" name="show_date" class="form-control" required>
                        </div>

                        <!-- Start Time -->
                        <div class="col-md-4">
                            <label>Start Time</label>
                            <input type="time" id="startTime" name="start_time" class="form-control" required>
                        </div>

                        <!-- End time --> <!-- readonly kasi it will calculated automatically -->
                        <div class="col-md-4">
                            <label>End Time</label>
                            <input type="time" id="endTime" name="end_time" class="form-control" readonly>
                        </div>

                        <!-- Ticket price -->
                        <div class="col-md-4 mt-3">
                            <label>Ticket Price</label>
                            <input type="number" step="0.01" name="ticket_price" class="form-control" required>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                            <button class="btn btn-warning" type="submit">Save Schedule</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>