<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class SubmissionGradeData extends Data
{
    public function __construct(
        public readonly int $submission_id,
        public readonly float $score,
        public readonly ?string $feedback,
    ) {}
}
