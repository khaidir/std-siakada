<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class InternshipLogData extends Data
{
    public function __construct(
        public readonly int $internship_id,
        public readonly string $date,
        public readonly string $activity,
        public readonly ?string $notes,
    ) {}
}
