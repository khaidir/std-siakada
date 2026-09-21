<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class GradeData extends Data
{
    public function __construct(
        public int $study_plan_detail_id,
        public int $student_id,
        public int $course_offering_id,
        public ?float $assignment_score = null,
        public ?float $midterm_score = null,
        public ?float $final_score = null,
        public ?float $score = null,
    ) {}
}
