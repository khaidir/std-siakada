<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class AttendanceData extends Data
{
    public function __construct(
        public int $course_offering_id,
        public int $student_id,
        public int $meeting_number,
        public string $date,
        public string $status,
    ) {}
}
