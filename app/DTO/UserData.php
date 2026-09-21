<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public string $role,
        public ?string $nim = null,
        public ?int $study_program_id = null,
        public ?string $entry_year = null,
        public ?string $nidn = null,
        public ?string $academic_rank = null,
    ) {}
}
