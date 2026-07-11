<!-- only validates inputs, it doesn't insert data or calculate times -->

<?php

class ScheduleValidator{
    public function validate(array $data): array{
        $errors = [];

        if (empty($data['movie_id'])) {
            $errors[] = "Please select a movie.";
        }

        if (empty($data['hall_id'])) {
            $errors[] = "Please select a cinema hall.";
        }

        if (empty($data['show_date'])) {
            $errors[] = "Please select a date.";
        }

        if (empty($data['start_time'])) {
            $errors[] = "Please select a start time.";
        }

        if (!empty($data['show_date']) && strtotime($data['show_date']) < strtotime(date("Y-m-d"))) {
            $errors[] = "Show date cannot be in the past.";
        }

        if (!isset($data['ticket_price']) || $data['ticket_price'] === '') {
            $errors[] = "Please enter the ticket price.";
        }

        if (!empty($data['ticket_price']) && $data['ticket_price'] < 0) {
            $errors[] = "Ticket price cannot be negative.";
        }

        return $errors;
    }
}