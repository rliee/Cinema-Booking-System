<?php
// the only class allowed to communicate with sql

require_once "SchedulingEngine.php";

// class skeleton
class ScheduleRepository
{
    private mysqli $conn;
    private SchedulingEngine $engine;

    // -- constructor --
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
        $this->engine = new SchedulingEngine();
    }

    // -- private helper methods --
    private function buildResponse(bool $success, string $message, array $extra = []): array
    {
        return array_merge(
            [
                "success" => $success,
                "message" => $message
            ],
            $extra
        );
    }

    // check if the schedule already has bookings
    private function hasBookings(int $scheduleId): bool
    {
        $sql = "SELECT COUNT(*) AS total
            FROM bookings
            WHERE schedule_id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $scheduleId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int)$row["total"] > 0;
    }

    // get movie duration
    private function getMovieDuration(int $movieId): int
    {
        $sql = "SELECT duration FROM movies WHERE movie_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movieId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($movie = $result->fetch_assoc()) {
            return (int)$movie['duration'];
        }
        return 0;
    }

    // check if the cinema hall exists
    private function hallExists(int $hallId): bool
    {
        $sql = "SELECT 1
            FROM cinema_halls
            WHERE hall_id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $hallId);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }

    private function getStatus(string $showDate, string $startTime, string $endTime): string
    {
        $now = new DateTime("now");
        $start = new DateTime("$showDate $startTime");
        $end = new DateTime("$showDate $endTime");

        if ($now < $start) {
            return "Coming Soon";
        }
        if ($now >= $start && $now <= $end) {
            return "Now Showing";
        }
        return "Completed";
    }

    // -- public business logic --
    // check conflict
    public function checkConflict(int $hallId, string $date, string $startTime, string $endTime, ?int $excludeScheduleId = null): bool
    {
        $sql = "SELECT 1 FROM show_schedules WHERE hall_id = ? AND show_date = ? AND start_time < ? AND end_time > ?";

        if ($excludeScheduleId !== null) {
            $sql .= " AND schedule_id <> ?";
        }

        $stmt = $this->conn->prepare($sql);
        if ($excludeScheduleId === null) {
            $stmt->bind_param("isss", $hallId, $date, $endTime, $startTime);
        } else {
            $stmt->bind_param("isssi", $hallId, $date, $endTime, $startTime, $excludeScheduleId);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // get the last schedule of the day
    public function getLastScheduleOfHall(int $hallId, string $date)
    {
        $sql = "SELECT end_time
        FROM show_schedules
        WHERE hall_id = ? AND show_date = ?
        ORDER BY end_time DESC
        LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $hallId, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // recommend next available start time
    public function getRecommendedStartTime(int $hallId, string $date): ?string
    {
        $last = $this->getLastScheduleOfHall($hallId, $date);
        if (!$last) {
            return null;
        }

        $earliest = $this->engine->calculateEarliestAvailableTime($last["end_time"]);

        return $this->engine->roundToSlot($earliest);
    }

    // -- CRUD operations --
    // insert schedule
    public function insert(array $data): array
    {
        $duration = $this->getMovieDuration($data['movie_id']);
        if ($duration <= 0) {
            return $this->buildResponse(
                false,
                "The selected movie is invalid."
            );
        }

        if (!$this->hallExists($data["hall_id"])) {
            return $this->buildResponse(
                false,
                "The selected cinema hall does not exist."
            );
        }

        $movieEnd = $this->engine->calculateMovieEndTime(
            $data['start_time'],
            $duration
        );

        if ($this->checkConflict(
            $data['hall_id'],
            $data['show_date'],
            $data['start_time'],
            $movieEnd
        )) {
            return $this->buildResponse(
                false,
                "Schedule conflict.",
                [
                    "recommended_time" => $this->getRecommendedStartTime(
                        $data['hall_id'],
                        $data['show_date']
                    )
                ]
            );
        }

        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO show_schedules
                (movie_id, hall_id, show_date, start_time, end_time)
                VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "iisss",
                $data['movie_id'],
                $data['hall_id'],
                $data['show_date'],
                $data['start_time'],
                $movieEnd
            );

            if (!$stmt->execute()) {
                throw new Exception("Unable to save schedule.");
            }
            $this->conn->commit();

            return $this->buildResponse(
                true,
                "Schedule created successfully.",
                [
                    "schedule_id" => $this->conn->insert_id
                ]
            );
        } catch (Exception $e) {
            $this->conn->rollback();

            return $this->buildResponse(
                false,
                $e->getMessage()
            );
        }
    }

    // update schedule
    public function update(int $scheduleId, array $data): array
    {
        $duration = $this->getMovieDuration($data["movie_id"]);
        if ($duration <= 0) {
            return $this->buildResponse(
                false,
                "The selected movie is invalid."
            );
        }

        if (!$this->hallExists($data["hall_id"])) {
            return $this->buildResponse(
                false,
                "The selected cinema hall does not exist."
            );
        }

        $movieEnd = $this->engine->calculateMovieEndTime(
            $data["start_time"],
            $duration
        );

        if ($this->checkConflict($data["hall_id"], $data["show_date"], $data["start_time"], $movieEnd, $scheduleId)) {
            return $this->buildResponse(
                false,
                "Schedule conflict.",
                ["recommended_time" => $this->getRecommendedStartTime(
                    $data["hall_id"],
                    $data["show_date"]
                )]
            );
        }

        $this->conn->begin_transaction();
        try {
            $sql = "UPDATE show_schedules
                SET
                    movie_id = ?,
                    hall_id = ?,
                    show_date = ?,
                    start_time = ?,
                    end_time = ?
                WHERE schedule_id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "iisssi",
                $data["movie_id"],
                $data["hall_id"],
                $data["show_date"],
                $data["start_time"],
                $movieEnd,
                $scheduleId
            );

            if (!$stmt->execute()) {
                throw new Exception("Unable to update schedule.");
            }

            $this->conn->commit();
            return $this->buildResponse(
                true,
                "Schedule updated successfully."
            );
        } catch (Exception $e) {
            $this->conn->rollback();
            return $this->buildResponse(
                false,
                $e->getMessage()
            );
        }
    }

    // delete schedule
    public function delete(int $scheduleId): array
    {
        if ($this->hasBookings($scheduleId)) {
            return $this->buildResponse(
                false,
                "Cannot delete a schedule that already has bookings."
            );
        }
        $this->conn->begin_transaction();

        try {
            $sql = "DELETE FROM show_schedules WHERE schedule_id = ? ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $scheduleId);

            if (!$stmt->execute()) {
                throw new Exception("Unable to delete schedule.");
            }
            $this->conn->commit();

            return $this->buildResponse(
                true,
                "Schedule deleted successfully."
            );
        } catch (Exception $e) {
            $this->conn->rollback();

            return $this->buildResponse(
                false,
                $e->getMessage()
            );
        }
    }

    // -- data retrieval --
    // get schedule by id
    public function getScheduleById(int $scheduleId): ?array
    {
        $sql = "SELECT *
            FROM show_schedules
            WHERE schedule_id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $scheduleId);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    // get movies available for scheduling
    public function getMovies(): array
    {
        $sql = "SELECT movie_id, title, duration, poster
            FROM movies
            WHERE status IN ('Coming Soon', 'Now Showing')
            ORDER BY title
        ";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // get cinema halls
    public function getHalls(): array
    {
        $sql = "SELECT hall_id, hall_name, total_seats
            FROM cinema_halls
            ORDER BY hall_name
        ";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // get schedule by date
    public function getSchedulesByDate(string $date): array
    {
        $sql = "SELECT ss.*, m.title, m.duration, m.poster, h.hall_name, h.total_seats,
            COALESCE(SUM(b.seats_booked),0) sold
            FROM show_schedules ss
            JOIN movies m
            ON ss.movie_id=m.movie_id
            JOIN cinema_halls h
            ON ss.hall_id=h.hall_id
            LEFT JOIN bookings b
            ON ss.schedule_id=b.schedule_id
            WHERE ss.show_date=?
            GROUP BY ss.schedule_id
            ORDER BY ss.start_time
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $schedules = [];
        while ($row = $result->fetch_assoc()) {
            $row["status"] = $this->getStatus(
                $row["show_date"],
                $row["start_time"],
                $row["end_time"]
            );

            $row["percent"] = 0;
            if ($row["total_seats"] > 0) {
                $row["percent"] = round(
                    ($row["sold"] / $row["total_seats"]) * 100,
                    2
                );
            }
            $schedules[] = $row;
        }
        return $schedules;
    }

    public function getSchedulesByWeek(string $week): array
    {
        // $week format: YYYY-WW (e.g. 2026-30)
        $start = new DateTime();
        $start->setISODate(
            (int) substr($week, 0, 4),
            (int) substr($week, 5, 2)
        );

        $end = clone $start;
        $end->modify("+6 days");

        $startDate = $start->format("Y-m-d");
        $endDate   = $end->format("Y-m-d");

        $sql = "SELECT ss.*, m.title, m.duration, m.poster,
                   h.hall_name, h.total_seats,
                   COALESCE(SUM(b.seats_booked),0) sold
            FROM show_schedules ss
            JOIN movies m
                ON ss.movie_id=m.movie_id
            JOIN cinema_halls h
                ON ss.hall_id=h.hall_id
            LEFT JOIN bookings b
                ON ss.schedule_id=b.schedule_id
            WHERE ss.show_date BETWEEN ? AND ?
            GROUP BY ss.schedule_id
            ORDER BY ss.show_date, ss.start_time";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();

        $result = $stmt->get_result();

        $schedules = [];

        while ($row = $result->fetch_assoc()) {
            $row["status"] = $this->getStatus(
                $row["show_date"],
                $row["start_time"],
                $row["end_time"]
            );

            $row["percent"] = 0;

            if ($row["total_seats"] > 0) {
                $row["percent"] = round(
                    ($row["sold"] / $row["total_seats"]) * 100,
                    2
                );
            }

            $schedules[] = $row;
        }

        return $schedules;
    }
}
