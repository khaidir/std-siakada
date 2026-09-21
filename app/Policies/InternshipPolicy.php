<?php

namespace App\Policies;

use App\Models\Internship;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class InternshipPolicy
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

    public function view(User $user, Internship $internship): bool
    {
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

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isMahasiswa($user);
    }

    public function update(User $user, Internship $internship): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $internship->student_id === $this->studentId($user);
        }

        if ($this->isDosen($user)) {
            return $internship->supervisor_id === $this->lecturerId($user);
        }

        return false;
    }

    public function delete(User $user, Internship $internship): bool
    {
        return $this->isSuperAdmin($user);
    }

    /**
     * Admin assign pembimbing KP.
     */
    public function assignSupervisor(User $user, Internship $internship): bool
    {
        return $this->isSuperAdmin($user);
    }
}
