<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class InternshipAssignData extends Data
{
    public function __construct(
        public readonly int $internship_id,
        public readonly int $supervisor_id,
    ) {}
}
