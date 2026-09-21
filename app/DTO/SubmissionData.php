<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class SubmissionData extends Data
{
    public function __construct(
        public int $assignment_id,
        public string $file_path,
    ) {}
}
