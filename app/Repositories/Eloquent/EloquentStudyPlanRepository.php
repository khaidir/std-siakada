<?php

namespace App\Repositories\Eloquent;

use App\Models\StudyPlan;
use App\Repositories\Contracts\StudyPlanRepository as StudyPlanRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentStudyPlanRepository implements StudyPlanRepositoryContract
{
    public function activeForStudent(int $studentId, int $semesterId): ?StudyPlan
    {
        return StudyPlan::query()
            ->select(['id', 'student_id', 'semester_id', 'status', 'approved_by', 'approved_at', 'created_at', 'updated_at'])
            ->where('student_id', $studentId)
            ->where('semester_id', $semesterId)
            ->latest()
            ->first();
    }

    public function createForStudent(int $studentId, int $semesterId): StudyPlan
    {
        return StudyPlan::query()->create([
            'student_id' => $studentId,
            'semester_id' => $semesterId,
            'status' => 'draft',
        ]);
    }

    public function save(StudyPlan $plan): void
    {
        $plan->save();
    }

    public function listForAdvisor(int $lecturerId): Collection
    {
        return StudyPlan::query()
            ->select(['id', 'student_id', 'semester_id', 'status', 'approved_by', 'approved_at', 'created_at', 'updated_at'])
            ->where('status', 'submitted')
            ->whereHas('student', function ($q) use ($lecturerId) {
                $q->select(['id', 'study_program_id'])
                    ->where('study_program_id', function ($sub) use ($lecturerId) {
                        $sub->select('study_program_id')
                            ->from('lecturers')
                            ->where('user_id', $lecturerId)
                            ->limit(1);
                    });
            })
            ->with([
                'student:id,name,study_program_id',
                'student.studyProgram:id,name',
                'semester:id,name,type',
            ])
            ->latest()
            ->get();
    }

    public function detail(int $planId): ?StudyPlan
    {
        return StudyPlan::query()
            ->select(['id', 'student_id', 'semester_id', 'status', 'approved_by', 'approved_at', 'created_at', 'updated_at'])
            ->where('id', $planId)
            ->with([
                'student:id,name,nim,study_program_id',
                'student.studyProgram:id,name',
                'semester:id,name,type',
                'studyPlanDetails:id,study_plan_id,course_offering_id,status',
                'studyPlanDetails.courseOffering:id,course_id,class,quota',
                'studyPlanDetails.courseOffering.course:id,code,name,credits,semester',
            ])
            ->first();
    }

    public function listAll(array $filters = []): LengthAwarePaginator
    {
        $query = StudyPlan::query()
            ->select(['id', 'student_id', 'semester_id', 'status', 'approved_by', 'approved_at', 'created_at', 'updated_at'])
            ->with([
                'student:id,name,nim,study_program_id',
                'student.studyProgram:id,name',
                'semester:id,name,type',
                'approvedBy:id,name',
            ]);

        // Filter by study program
        if (! empty($filters['prodi'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('study_program_id', $filters['prodi']);
            });
        }

        // Filter by faculty (via student's study program)
        if (! empty($filters['fakultas'])) {
            $query->whereHas('student.studyProgram', function ($q) use ($filters) {
                $q->where('faculty_id', $filters['fakultas']);
            });
        }

        // Filter by semester
        if (! empty($filters['semester'])) {
            $query->where('semester_id', $filters['semester']);
        }

        // Filter by status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(15);
    }
}
