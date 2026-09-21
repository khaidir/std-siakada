<?php

namespace App\Support;

use App\Enums\GradeLetter;

final class GradeCalculator
{
    /**
     * Hitung skor akhir dari komponen penilaian.
     *
     * Bila seluruh komponen (tugas, UTS, UAS) tersedia, kembalikan rata-rata
     * terbobot: Tugas 20% + UTS 30% + UAS 50%. Bila ada komponen yang kosong,
     * kembalikan null agar pemanggil memakai skor langsung.
     */
    public static function final(?float $assignment, ?float $midterm, ?float $final): ?float
    {
        if ($assignment === null || $midterm === null || $final === null) {
            return null;
        }

        return round((0.2 * $assignment) + (0.3 * $midterm) + (0.5 * $final), 2);
    }

    /**
     * Konversi skor akhir menjadi huruf dan bobot (grade point).
     *
     * @return array{0: GradeLetter, 1: float}
     */
    public static function letterAndPoint(float $score): array
    {
        $letter = match (true) {
            $score >= 85 => GradeLetter::A,
            $score >= 80 => GradeLetter::AMinus,
            $score >= 75 => GradeLetter::BPlus,
            $score >= 70 => GradeLetter::B,
            $score >= 65 => GradeLetter::BMinus,
            $score >= 60 => GradeLetter::CPlus,
            $score >= 55 => GradeLetter::C,
            $score >= 40 => GradeLetter::D,
            default => GradeLetter::E,
        };

        return [$letter, $letter->point()];
    }
}
