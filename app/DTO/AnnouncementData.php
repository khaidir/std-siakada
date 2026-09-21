<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class AnnouncementData extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public ?string $target_role = null,
        public ?string $published_at = null,
    ) {}
}
