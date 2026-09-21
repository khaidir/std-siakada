<?php

namespace App\DTO;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class BatchAttendanceData extends Data
{
    public function __construct(
        #[DataCollectionOf(AttendanceData::class)]
        public DataCollection $attendances,
    ) {}
}
