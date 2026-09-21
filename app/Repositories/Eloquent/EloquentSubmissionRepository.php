<?php

namespace App\Repositories\Eloquent;

use App\Models\AssignmentSubmission;
use App\Repositories\Contracts\SubmissionRepository as SubmissionRepositoryContract;
use Illuminate\Support\Collection;

class EloquentSubmissionRepository implements SubmissionRepositoryContract
{
    public function create(array $data): AssignmentSubmission
    {
        return AssignmentSubmission::query()->create($data);
    }

    public function update(int $id, array $data): AssignmentSubmission
    {
        $submission = AssignmentSubmission::query()->findOrFail($id);
        $submission->update($data);

        return $submission->fresh();
    }

    public function findByAssignmentAndStudent(int $assignmentId, int $studentId): ?AssignmentSubmission
    {
        return AssignmentSubmission::query()
            ->select(['id', 'assignment_id', 'student_id', 'file_path', 'submitted_at', 'score', 'feedback'])
            ->where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function listByAssignment(int $assignmentId): Collection
    {
        return AssignmentSubmission::query()
            ->select(['id', 'assignment_id', 'student_id', 'file_path', 'submitted_at', 'score', 'feedback'])
            ->with(['student:id,user_id,nim', 'student.user:id,name'])
            ->where('assignment_id', $assignmentId)
            ->orderBy('submitted_at')
            ->get();
    }

    public function gradeBatch(array $rows): void
    {
        foreach ($rows as $row) {
            AssignmentSubmission::query()
                ->where('id', $row['submission_id'])
                ->update([
                    'score' => $row['score'],
                    'feedback' => $row['feedback'] ?? null,
                ]);
        }
    }
}
