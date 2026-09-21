<?php

namespace App\Enums;

/**
 * Nilai huruf beserta bobot (grade point) untuk konversi nilai akhir.
 *
 * Backing value adalah label huruf yang disimpan di kolom grades.letter_grade.
 */
enum GradeLetter: string
{
    case A = 'A';
    case AMinus = 'A-';
    case BPlus = 'B+';
    case B = 'B';
    case BMinus = 'B-';
    case CPlus = 'C+';
    case C = 'C';
    case D = 'D';
    case E = 'E';

    /**
     * Bobot nilai (grade point) sesuai skala 4.0.
     */
    public function point(): float
    {
        return match ($this) {
            self::A => 4.0,
            self::AMinus => 3.75,
            self::BPlus => 3.25,
            self::B => 3.0,
            self::BMinus => 2.75,
            self::CPlus => 2.25,
            self::C => 2.0,
            self::D => 1.0,
            self::E => 0.0,
        };
    }

    /**
     * Label huruf yang ditampilkan.
     */
    public function label(): string
    {
        return $this->value;
    }
}
