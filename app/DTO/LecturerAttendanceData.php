<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class LecturerAttendanceData extends Data
{
    public function __construct(
        public readonly int $course_offering_id,
        public readonly string $date,
        public readonly ?string $check_in,
        public readonly ?string $check_out,
    ) {}
}
