<?php

namespace App\Services;

use App\Models\Student;
use App\Repositories\Contracts\GradeRepository;
use Illuminate\Support\Collection;

class TranskripService
{
    public function __construct(
        protected GradeRepository $gradeRepository,
        protected KhsService $khsService,
    ) {}

    /**
     * Data untuk halaman transkrip.
     *
     * @return array{student: Student, semesterGroups: Collection, ipk: float, totalSks: int}
     */
    public function pageData(Student $student): array
    {
        $allGrades = $this->gradeRepository->listForStudent($student->id);

        // Kelompokkan per semester
        $semesterGroups = $allGrades->groupBy(fn ($grade) => $grade->studyPlanDetail?->studyPlan?->semester_id);

        $result = collect();
        foreach ($semesterGroups as $semesterId => $grades) {
            $semester = \App\Models\Semester::find($semesterId);
            $ip = $this->khsService->calculateIp($grades);
            $sks = $this->khsService->calculateTotalSks($grades);

            $result->push([
                'semester' => $semester,
                'grades' => $grades,
                'ip' => $ip,
                'total_sks' => $sks,
            ]);
        }

        $ipk = $this->khsService->calculateIp($allGrades);
        $totalSks = $this->khsService->calculateTotalSks($allGrades);

        return [
            'student' => $student->load('user:id,name', 'studyProgram:id,name'),
            'semesterGroups' => $result,
            'ipk' => $ipk,
            'totalSks' => $totalSks,
        ];
    }
}
