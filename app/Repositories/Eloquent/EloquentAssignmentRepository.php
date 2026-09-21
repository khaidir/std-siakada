<?php

namespace App\Repositories\Eloquent;

use App\Models\Assignment;
use App\Models\StudyPlanDetail;
use App\Repositories\Contracts\AssignmentRepository as AssignmentRepositoryContract;
use Illuminate\Support\Collection;

class EloquentAssignmentRepository implements AssignmentRepositoryContract
{
    public function listForStudent(int $studentId): Collection
    {
        // Ambil course_offering_id dari study_plan_details milik mahasiswa
        // yang statusnya approved (KRS disetujui).
        $offeringIds = StudyPlanDetail::query()
            ->select(['course_offering_id'])
            ->whereHas('studyPlan', fn ($q) => $q->where('student_id', $studentId))
            ->where('status', 'approved')
            ->pluck('course_offering_id');

        if ($offeringIds->isEmpty()) {
            return collect();
        }

        return Assignment::query()
            ->select(['id', 'course_offering_id', 'title', 'description', 'due_date', 'max_score'])
            ->with([
                'courseOffering:id,course_id',
                'courseOffering.course:id,code,name',
                'assignmentSubmissions' => function ($q) use ($studentId) {
                    $q->select(['id', 'assignment_id', 'student_id', 'file_path', 'submitted_at', 'score', 'feedback'])
                        ->where('student_id', $studentId);
                },
            ])
            ->whereIn('course_offering_id', $offeringIds)
            ->orderBy('due_date')
            ->get();
    }

    public function listByOffering(int $offeringId): Collection
    {
        return Assignment::query()
            ->select(['id', 'course_offering_id', 'title', 'description', 'due_date', 'max_score'])
            ->withCount(['assignmentSubmissions as submissions_count'])
            ->where('course_offering_id', $offeringId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(array $data): Assignment
    {
        return Assignment::query()->create($data);
    }

    public function update(int $id, array $data): Assignment
    {
        $assignment = $this->findById($id);

        if (! $assignment) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                'Assignment not found.',
            );
        }

        $assignment->update($data);

        return $assignment->fresh();
    }

    public function delete(int $id): void
    {
        $assignment = $this->findById($id);

        if (! $assignment) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                'Assignment not found.',
            );
        }

        $assignment->delete();
    }

    public function findById(int $id): ?Assignment
    {
        return Assignment::query()
            ->select(['id', 'course_offering_id', 'title', 'description', 'due_date', 'max_score'])
            ->where('id', $id)
            ->first();
    }
}
