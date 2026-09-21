<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class AcademicYearData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public string $start_date,
        public string $end_date,
        public bool $is_active = false,
    ) {}
}
