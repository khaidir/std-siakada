<?php

namespace App\Repositories\Eloquent;

use App\Models\CourseOffering;
use App\Models\Student;
use App\Repositories\Contracts\CourseOfferingRepository as CourseOfferingRepositoryContract;
use Illuminate\Support\Collection;

class CourseOfferingRepository implements CourseOfferingRepositoryContract
{
    public function listByLecturer(int $lecturerId): Collection
    {
        return CourseOffering::query()
            ->select(['id', 'course_id', 'semester_id', 'day', 'start_time', 'end_time'])
            ->with([
                'course:id,code,name,sks',
                'semester:id,type',
            ])
            ->where('lecturer_id', $lecturerId)
            ->orderBy('start_time')
            ->get();
    }

    public function availableForStudent(int $studentId, int $semesterId): Collection
    {
        $student = Student::query()
            ->select(['id', 'study_program_id'])
            ->where('id', $studentId)
            ->first();

        if (! $student) {
            return collect();
        }

        return CourseOffering::query()
            ->select(['id', 'course_id', 'semester_id', 'lecturer_id', 'classroom_id', 'day', 'start_time', 'end_time', 'quota'])
            ->with([
                'course:id,code,name,sks,study_program_id',
                'lecturer:id,user_id',
                'lecturer.user:id,name',
                'classroom:id,name',
            ])
            ->where('semester_id', $semesterId)
            ->whereHas('course', fn ($q) => $q->where('study_program_id', $student->study_program_id))
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();
    }

    public function scheduleForStudent(int $studentId, int $semesterId): Collection
    {
        return CourseOffering::query()
            ->select(['id', 'course_id', 'lecturer_id', 'classroom_id', 'day', 'start_time', 'end_time'])
            ->with([
                'course:id,code,name,sks',
                'lecturer:id,user_id',
                'lecturer.user:id,name',
                'classroom:id,name',
            ])
            ->where('semester_id', $semesterId)
            ->whereHas('studyPlanDetails', fn ($q) => $q
                ->whereHas('studyPlan', fn ($sq) => $sq
                    ->where('student_id', $studentId)
                    ->where('status', 'approved')
                )
                ->where('status', 'approved')
            )
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();
    }

    public function listAll(): Collection
    {
        return CourseOffering::query()
            ->select(['id', 'course_id', 'semester_id', 'lecturer_id', 'classroom_id', 'day', 'start_time', 'end_time', 'quota'])
            ->with([
                'course:id,code,name',
                'lecturer:id,user_id',
                'lecturer.user:id,name',
                'classroom:id,name',
                'semester:id,type,academic_year_id',
                'semester.academicYear:id,code,name',
            ])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();
    }

    public function create(array $data): CourseOffering
    {
        return CourseOffering::query()->create($data);
    }

    public function update(int $id, array $data): CourseOffering
    {
        $offering = CourseOffering::query()->findOrFail($id);
        $offering->update($data);

        return $offering->fresh();
    }

    public function delete(int $id): bool
    {
        return CourseOffering::query()->where('id', $id)->delete();
    }

    public function findById(int $id): ?CourseOffering
    {
        return CourseOffering::query()
            ->select(['id', 'course_id', 'semester_id', 'lecturer_id', 'classroom_id', 'day', 'start_time', 'end_time', 'quota'])
            ->where('id', $id)
            ->first();
    }
}
