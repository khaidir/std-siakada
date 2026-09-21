<?php

namespace App\Repositories\Eloquent;

use App\Models\Grade;
use App\Repositories\Contracts\GradeRepository as GradeRepositoryContract;
use Illuminate\Support\Collection;

class GradeRepository implements GradeRepositoryContract
{
    public function listByOffering(int $offeringId): Collection
    {
        return Grade::query()
            ->select(['id', 'study_plan_detail_id', 'student_id', 'score', 'letter_grade', 'grade_point'])
            ->with('student:id,user_id,nim', 'student.user:id,name')
            ->where('course_offering_id', $offeringId)
            ->orderBy('id')
            ->get();
    }

    public function listForStudent(int $studentId): Collection
    {
        return Grade::query()
            ->select(['id', 'study_plan_detail_id', 'student_id', 'course_offering_id', 'score', 'letter_grade', 'grade_point'])
            ->with([
                'studyPlanDetail:id,study_plan_id,course_offering_id',
                'studyPlanDetail.courseOffering:id,course_id',
                'studyPlanDetail.courseOffering.course:id,code,name,sks',
            ])
            ->where('student_id', $studentId)
            ->orderBy('id')
            ->get();
    }

    public function listForStudentBySemester(int $studentId, int $semesterId): Collection
    {
        return Grade::query()
            ->select(['id', 'study_plan_detail_id', 'student_id', 'course_offering_id', 'score', 'letter_grade', 'grade_point'])
            ->with([
                'studyPlanDetail:id,study_plan_id,course_offering_id',
                'studyPlanDetail.studyPlan:id,semester_id',
                'studyPlanDetail.courseOffering:id,course_id',
                'studyPlanDetail.courseOffering.course:id,code,name,sks,semester',
            ])
            ->where('student_id', $studentId)
            ->whereHas('studyPlanDetail.studyPlan', fn ($q) => $q->where('semester_id', $semesterId))
            ->orderBy('id')
            ->get();
    }

    public function upsertBatch(array $rows): void
    {
        if ($rows === []) {
            return;
        }

        Grade::upsert(
            $rows,
            ['study_plan_detail_id'],
            [
                'student_id',
                'course_offering_id',
                'assignment_score',
                'midterm_score',
                'final_score',
                'score',
                'letter_grade',
                'grade_point',
            ],
        );
    }
}
