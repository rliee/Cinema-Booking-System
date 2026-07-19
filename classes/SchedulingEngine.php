<?php
// only calculates times, doesn't communicate to the database

class SchedulingEngine{
    private int $turnoverMinutes = 30;

    /* returns the movie end time only */
    public function calculateMovieEndTime(string $startTime, int $duration): string{
        $time = new DateTime($startTime);
        $time->modify("+{$duration} minutes");
        return $time->format("H:i:s");
    }

    /* returns the first available time after turnover */
    public function calculateEarliestAvailableTime(string $movieEnd): string{
        $time = new DateTime($movieEnd);
        $time->modify("+{$this->turnoverMinutes} minutes");
        return $time->format("H:i:s");
    }

    /* round up to the nearest :00 or :30 */
    public function roundToSlot(string $timeString): string{
        $time = new DateTime($timeString);
        $minutes = (int)$time->format("i");

        if ($minutes == 0 || $minutes == 30) {
            return $time->format("H:i:s");
        }

        if ($minutes < 30) {
            $time->setTime(
                (int)$time->format("H"),
                30
            );
        } else {
            $time->modify("+1 hour");
            $time->setTime(
                (int)$time->format("H"),
                0
            );
        }
        return $time->format("H:i:s");
    }

    /* complete pipeline */
    public function getRecommendedNextStartTime(string $startTime, int $duration): string{
        $movieEnd =
            $this->calculateMovieEndTime(
                $startTime,
                $duration
            );

        $earliest =
            $this->calculateEarliestAvailableTime(
                $movieEnd
            );

        return $this->roundToSlot($earliest);
    }
}