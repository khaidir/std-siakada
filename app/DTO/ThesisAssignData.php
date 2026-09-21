<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ThesisAssignData extends Data
{
    public function __construct(
        public readonly int $thesis_id,
        public readonly int $supervisor_1_id,
        public readonly ?int $supervisor_2_id,
    ) {}
}
