<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class CourseOfferingData extends Data
{
    public function __construct(
        public int $course_id,
        public int $semester_id,
        public int $lecturer_id,
        public int $classroom_id,
        public string $day,
        public string $start_time,
        public string $end_time,
        public int $quota,
    ) {}
}
