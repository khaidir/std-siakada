<?php

namespace App\Repositories\Eloquent;

use App\Models\Grade;
use App\Models\Student;
use App\Repositories\Contracts\StudentRepository as StudentRepositoryContract;

class StudentRepository implements StudentRepositoryContract
{
    public function recalculateGpaAndSks(int $studentId): void
    {
        $grades = Grade::query()
            ->select(['id', 'student_id', 'grade_point', 'course_offering_id'])
            ->with('courseOffering:id,course_id', 'courseOffering.course:id,sks')
            ->where('student_id', $studentId)
            ->get();

        $totalSks = 0;
        $totalPoints = 0.0;

        foreach ($grades as $grade) {
            $sks = (int) ($grade->courseOffering?->course?->sks ?? 0);

            if ($sks <= 0) {
                continue;
            }

            $totalSks += $sks;
            $totalPoints += (float) $grade->grade_point * $sks;
        }

        $gpa = $totalSks > 0 ? round($totalPoints / $totalSks, 2) : 0.0;

        Student::where('id', $studentId)->update([
            'gpa' => $gpa,
            'total_sks' => $totalSks,
        ]);
    }

    public function create(array $data): Student
    {
        return Student::query()->create($data);
    }

    public function findByUserId(int $userId): ?Student
    {
        return Student::query()
            ->select(['id', 'user_id', 'nim', 'study_program_id', 'entry_year', 'status'])
            ->where('user_id', $userId)
            ->first();
    }
}
