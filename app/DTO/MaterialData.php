<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class MaterialData extends Data
{
    public function __construct(
        public int $course_offering_id,
        public string $title,
        public ?string $description = null,
        public ?string $file_path = null,
        public ?int $uploaded_by = null,
    ) {}
}
