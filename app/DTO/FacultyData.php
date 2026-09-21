<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class FacultyData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
    ) {}
}
