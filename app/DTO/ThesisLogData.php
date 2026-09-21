<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ThesisLogData extends Data
{
    public function __construct(
        public readonly int $thesis_id,
        public readonly string $date,
        public readonly string $activity,
        public readonly ?string $notes,
    ) {}
}
