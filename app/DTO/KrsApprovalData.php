<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class KrsApprovalData extends Data
{
    public function __construct(
        public readonly int $study_plan_id,
        public readonly string $decision, // 'approved' | 'rejected'
        public readonly ?string $reason,
    ) {}
}
