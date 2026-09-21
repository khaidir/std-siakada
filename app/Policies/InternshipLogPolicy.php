<?php

namespace App\Policies;

use App\Models\Internship;
use App\Models\InternshipLog;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class InternshipLogPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $this->isKaprodi($user)
            || $this->isDosen($user)
            || $this->isMahasiswa($user)
            || $this->isPimpinan($user);
    }

    public function view(User $user, InternshipLog $log): bool
    {
        return $this->canViewInternship($user, $log->internship);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isMahasiswa($user);
    }

    public function update(User $user, InternshipLog $log): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isMahasiswa($user) && $log->internship?->student_id === $this->studentId($user);
    }

    /**
     * Dosen pembimbing approve logbook.
     */
    public function approve(User $user, InternshipLog $log): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isDosen($user)
            && $log->internship?->supervisor_id === $this->lecturerId($user);
    }

    public function delete(User $user, InternshipLog $log): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isMahasiswa($user) && $log->internship?->student_id === $this->studentId($user);
    }

    private function canViewInternship(User $user, ?Internship $internship): bool
    {
        if ($internship === null) {
            return false;
        }

        if ($this->isSuperAdmin($user) || $this->isPimpinan($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $internship->student_id === $this->studentId($user);
        }

        if ($this->isDosen($user)) {
            return $internship->supervisor_id === $this->lecturerId($user);
        }

        if ($this->isKaprodi($user)) {
            return $internship->student?->study_program_id === $this->studyProgramId($user);
        }

        return false;
    }
}
