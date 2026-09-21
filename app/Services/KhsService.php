<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\Student;
use App\Repositories\Contracts\GradeRepository;
use Illuminate\Support\Collection;

class KhsService
{
    public function __construct(
        protected GradeRepository $gradeRepository,
    ) {}

    /**
     * Data untuk halaman KHS.
     *
     * @return array{semesters: Collection, selectedSemesterId: int|null, grades: Collection, ipSemester: float, ipk: float, totalSksSemester: int, totalSksKumulatif: int}
     */
    public function pageData(Student $student, ?int $semesterId = null): array
    {
        // Semua semester yang sudah ditempuh (punya KRS approved)
        $semesters = Semester::query()
            ->select(['id', 'academic_year_id', 'type'])
            ->with('academicYear:id,code,name')
            ->whereHas('studyPlans', fn ($q) => $q->where('student_id', $student->id)->where('status', 'approved'))
            ->orderBy('id')
            ->get();

        if ($semesters->isEmpty()) {
            return [
                'semesters' => collect(),
                'selectedSemesterId' => null,
                'grades' => collect(),
                'ipSemester' => 0.0,
                'ipk' => 0.0,
                'totalSksSemester' => 0,
                'totalSksKumulatif' => 0,
            ];
        }

        // Jika tidak ada semesterId yang dipilih, pakai semester terakhir
        if (! $semesterId || ! $semesters->firstWhere('id', $semesterId)) {
            $semesterId = $semesters->last()->id;
        }

        $grades = $this->gradeRepository->listForStudentBySemester($student->id, $semesterId);

        $ipSemester = $this->calculateIp($grades);
        $totalSksSemester = $this->calculateTotalSks($grades);

        // IPK kumulatif dari semua semester
        $allGrades = $this->gradeRepository->listForStudent($student->id);
        $ipk = $this->calculateIp($allGrades);
        $totalSksKumulatif = $this->calculateTotalSks($allGrades);

        return [
            'semesters' => $semesters,
            'selectedSemesterId' => $semesterId,
            'grades' => $grades,
            'ipSemester' => $ipSemester,
            'ipk' => $ipk,
            'totalSksSemester' => $totalSksSemester,
            'totalSksKumulatif' => $totalSksKumulatif,
        ];
    }

    /**
     * Hitung IP (Indeks Prestasi) = Σ(grade_point × sks) / Σ(sks).
     */
    public function calculateIp(Collection $grades): float
    {
        $totalBobot = 0;
        $totalSks = 0;

        foreach ($grades as $grade) {
            $sks = $grade->studyPlanDetail?->courseOffering?->course?->sks ?? 0;
            $totalBobot += ($grade->grade_point ?? 0) * $sks;
            $totalSks += $sks;
        }

        if ($totalSks === 0) {
            return 0.0;
        }

        return round($totalBobot / $totalSks, 2);
    }

    /**
     * Hitung total SKS yang sudah ditempuh (dari grades).
     */
    public function calculateTotalSks(Collection $grades): int
    {
        $total = 0;
        foreach ($grades as $grade) {
            $total += $grade->studyPlanDetail?->courseOffering?->course?->sks ?? 0;
        }

        return $total;
    }
}
