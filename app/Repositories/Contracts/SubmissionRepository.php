<?php

namespace App\Repositories\Contracts;

use App\Models\AssignmentSubmission;
use Illuminate\Support\Collection;

interface SubmissionRepository
{
    public function create(array $data): AssignmentSubmission;

    public function update(int $id, array $data): AssignmentSubmission;

    /**
     * Cari submission milik mahasiswa untuk suatu tugas.
     */
    public function findByAssignmentAndStudent(int $assignmentId, int $studentId): ?AssignmentSubmission;

    /**
     * Daftar submission berdasarkan tugas.
     *
     * @return Collection<int, AssignmentSubmission>
     */
    public function listByAssignment(int $assignmentId): Collection;

    /**
     * Update skor & feedback secara batch.
     *
     * @param  array<int, array{submission_id: int, score: float, feedback: ?string}>  $rows
     */
    public function gradeBatch(array $rows): void;
}
