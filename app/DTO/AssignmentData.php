<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class AssignmentData extends Data
{
    public function __construct(
        public readonly int $course_offering_id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $due_date,
        public readonly float $max_score,
    ) {}
}
