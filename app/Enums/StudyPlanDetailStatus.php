<?php

namespace App\Enums;

enum StudyPlanDetailStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
