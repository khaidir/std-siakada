<?php

namespace App\Policies;

use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class AssignmentSubmissionPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isDosen($user) || $this->isMahasiswa($user);
    }

    public function view(User $user, AssignmentSubmission $submission): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $submission->student_id === $this->studentId($user);
        }

        if ($this->isDosen($user)) {
            return $submission->assignment?->courseOffering?->lecturer_id === $this->lecturerId($user);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isMahasiswa($user);
    }

    public function update(User $user, AssignmentSubmission $submission): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isMahasiswa($user) && $submission->student_id === $this->studentId($user);
    }

    /**
     * Penilaian submission oleh dosen pengampu kelas terkait.
     */
    public function grade(User $user, AssignmentSubmission $submission): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isDosen($user)
            && $submission->assignment?->courseOffering?->lecturer_id === $this->lecturerId($user);
    }

    public function delete(User $user, AssignmentSubmission $submission): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isMahasiswa($user) && $submission->student_id === $this->studentId($user);
    }
}
