<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class SemesterData extends Data
{
    public function __construct(
        public int $academic_year_id,
        public string $type,
        public string $start_date,
        public string $end_date,
        public bool $is_active = false,
    ) {}
}
