<?php

namespace App\Enums;

enum InternshipStatus: string
{
    case Draft = 'draft';
    case Berjalan = 'berjalan';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
}
