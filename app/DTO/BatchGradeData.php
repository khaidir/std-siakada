<?php

namespace App\DTO;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class BatchGradeData extends Data
{
    public function __construct(
        #[DataCollectionOf(GradeData::class)]
        public DataCollection $grades,
    ) {}
}
