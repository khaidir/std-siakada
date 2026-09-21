<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Aktif = 'aktif';
    case Cuti = 'cuti';
    case Lulus = 'lulus';
    case DO = 'do';
    case Nonaktif = 'nonaktif';
}
