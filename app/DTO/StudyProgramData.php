<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class StudyProgramData extends Data
{
    public function __construct(
        public int $faculty_id,
        public string $code,
        public string $name,
        public string $degree_level,
    ) {}
}
