<?php

use App\Enums\AcademicRank;
use App\Enums\AttendanceStatus;
use App\Enums\CourseType;
use App\Enums\DayOfWeek;
use App\Enums\DegreeLevel;
use App\Enums\GradeLetter;
use App\Enums\InternshipLogApproval;
use App\Enums\InternshipStatus;
use App\Enums\LecturerAttendanceStatus;
use App\Enums\SemesterType;
use App\Enums\StudentStatus;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Enums\ThesisStatus;

function valuesOf(string $enum): array
{
    return array_column($enum::cases(), 'value');
}

it('mendefinisikan DegreeLevel sesuai PRD', function () {
    expect(valuesOf(DegreeLevel::class))->toBe(['d3', 'd4', 's1', 's2', 's3']);
});

it('mendefinisikan SemesterType sesuai PRD', function () {
    expect(valuesOf(SemesterType::class))->toBe(['ganjil', 'genap']);
});

it('mendefinisikan CourseType sesuai PRD', function () {
    expect(valuesOf(CourseType::class))->toBe(['wajib', 'pilihan']);
});

it('mendefinisikan DayOfWeek sesuai PRD', function () {
    expect(valuesOf(DayOfWeek::class))->toBe([
        'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu',
    ]);
});

it('mendefinisikan StudentStatus sesuai PRD', function () {
    expect(valuesOf(StudentStatus::class))->toBe(['aktif', 'cuti', 'lulus', 'do', 'nonaktif']);
});

it('mendefinisikan AcademicRank sesuai PRD', function () {
    expect(valuesOf(AcademicRank::class))->toBe(['asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar']);
});

it('mendefinisikan StudyPlanStatus sesuai PRD', function () {
    expect(valuesOf(StudyPlanStatus::class))->toBe(['draft', 'submitted', 'approved', 'rejected']);
});

it('mendefinisikan StudyPlanDetailStatus sesuai PRD', function () {
    expect(valuesOf(StudyPlanDetailStatus::class))->toBe(['pending', 'approved', 'rejected']);
});

it('mendefinisikan AttendanceStatus sesuai PRD', function () {
    expect(valuesOf(AttendanceStatus::class))->toBe(['hadir', 'izin', 'sakit', 'alpha']);
});

it('mendefinisikan LecturerAttendanceStatus sesuai PRD', function () {
    expect(valuesOf(LecturerAttendanceStatus::class))->toBe(['hadir', 'terlambat', 'izin', 'alpha']);
});

it('mendefinisikan ThesisStatus sesuai PRD', function () {
    expect(valuesOf(ThesisStatus::class))->toBe(['proposal', 'seminar_proposal', 'sidang', 'lulus', 'revisi']);
});

it('mendefinisikan InternshipStatus sesuai PRD', function () {
    expect(valuesOf(InternshipStatus::class))->toBe(['draft', 'berjalan', 'selesai', 'ditolak']);
});

it('mendefinisikan InternshipLogApproval sesuai PRD', function () {
    expect(valuesOf(InternshipLogApproval::class))->toBe(['pending', 'approved', 'rejected']);
});

it('GradeLetter memiliki label huruf dan bobot sesuai PRD', function () {
    expect(GradeLetter::A->value)->toBe('A')
        ->and(GradeLetter::A->point())->toBe(4.0)
        ->and(GradeLetter::AMinus->value)->toBe('A-')
        ->and(GradeLetter::AMinus->point())->toBe(3.75)
        ->and(GradeLetter::BPlus->value)->toBe('B+')
        ->and(GradeLetter::BPlus->point())->toBe(3.25)
        ->and(GradeLetter::B->point())->toBe(3.0)
        ->and(GradeLetter::BMinus->value)->toBe('B-')
        ->and(GradeLetter::BMinus->point())->toBe(2.75)
        ->and(GradeLetter::CPlus->point())->toBe(2.25)
        ->and(GradeLetter::C->point())->toBe(2.0)
        ->and(GradeLetter::D->point())->toBe(1.0)
        ->and(GradeLetter::E->point())->toBe(0.0);
});
