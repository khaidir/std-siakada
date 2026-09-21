<?php

use App\Enums\GradeLetter;
use App\Support\GradeCalculator;

it('menghitung skor akhir dari komponen 20/30/50', function () {
    expect(GradeCalculator::final(80, 70, 90))->toBe(82.0)
        ->and(GradeCalculator::final(85, 85, 85))->toBe(85.0)
        ->and(GradeCalculator::final(81, 79, 88))->toBe(83.9);
});

it('mengembalikan null bila ada komponen yang kosong', function () {
    expect(GradeCalculator::final(80, null, 90))->toBeNull()
        ->and(GradeCalculator::final(null, 70, 90))->toBeNull()
        ->and(GradeCalculator::final(80, 70, null))->toBeNull()
        ->and(GradeCalculator::final(null, null, null))->toBeNull();
});

it('mengonversi skor ke huruf dan grade point pada semua batas', function (float $score, string $letter, float $point) {
    [$actualLetter, $actualPoint] = GradeCalculator::letterAndPoint($score);

    expect($actualLetter)->toBe(GradeLetter::from($letter))
        ->and($actualPoint)->toBe($point);
})->with([
    'skor 0 → E' => [0.0, 'E', 0.0],
    'skor 39 → E' => [39.0, 'E', 0.0],
    'skor 40 → D' => [40.0, 'D', 1.0],
    'skor 54 → D' => [54.0, 'D', 1.0],
    'skor 55 → C' => [55.0, 'C', 2.0],
    'skor 59 → C' => [59.0, 'C', 2.0],
    'skor 60 → C+' => [60.0, 'C+', 2.25],
    'skor 64 → C+' => [64.0, 'C+', 2.25],
    'skor 65 → B-' => [65.0, 'B-', 2.75],
    'skor 69 → B-' => [69.0, 'B-', 2.75],
    'skor 70 → B' => [70.0, 'B', 3.0],
    'skor 74 → B' => [74.0, 'B', 3.0],
    'skor 75 → B+' => [75.0, 'B+', 3.25],
    'skor 79 → B+' => [79.0, 'B+', 3.25],
    'skor 80 → A-' => [80.0, 'A-', 3.75],
    'skor 84 → A-' => [84.0, 'A-', 3.75],
    'skor 85 → A' => [85.0, 'A', 4.0],
    'skor 100 → A' => [100.0, 'A', 4.0],
]);
