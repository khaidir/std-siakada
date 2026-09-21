<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ClassroomData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public int $capacity,
        public ?string $building = null,
    ) {}
}
