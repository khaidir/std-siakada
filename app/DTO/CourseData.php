<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class CourseData extends Data
{
    public function __construct(
        public ?int $study_program_id = null,
        public string $code,
        public string $name,
        public int $sks,
        public int $semester,
        public string $type,
    ) {}
}
