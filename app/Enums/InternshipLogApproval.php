<?php

namespace App\Enums;

enum InternshipLogApproval: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
