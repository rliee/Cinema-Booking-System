<?php
// only validates inputs, it doesn't insert data or calculate times 

class ScheduleValidator {
    public function validate(array $data): string {
        if (empty($data['movie_id'])) {
            return "Please select a movie.";
        }

        if (empty($data['hall_id'])) {
            return "Please select a cinema hall.";
        }

        if (empty($data['show_date'])) {
            return "Please select a date.";
        }

        if (empty($data['start_time'])) {
            return "Please select a start time.";
        }

        if (
            !empty($data['show_date']) &&
            strtotime($data['show_date']) < strtotime(date("Y-m-d"))
        ) {
            return "Show date cannot be in the past.";
        }

        // validation passed
        return "";
    }
}