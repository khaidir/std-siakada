<?php

namespace App\Enums;

enum LecturerAttendanceStatus: string
{
    case Hadir = 'hadir';
    case Terlambat = 'terlambat';
    case Izin = 'izin';
    case Alpha = 'alpha';
}
